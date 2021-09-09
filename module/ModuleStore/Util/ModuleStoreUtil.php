<?php


namespace Module\ModuleStore\Util;


use Chumper\Zipper\Zipper;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\CurlUtil;
use ModStart\Core\Util\FileUtil;
use ModStart\Module\ModuleManager;

class ModuleStoreUtil
{
    const REMOTE_BASE = 'https://modstart.com';

    public static function remoteModuleData()
    {
        return CurlUtil::getJSONData(self::REMOTE_BASE . '/api/store/module');
    }

    public static function all()
    {
        $remoteModuleResult = self::remoteModuleData();
        $categories = [];
        if (!empty($remoteModuleResult['data']['categories'])) {
            $categories = $remoteModuleResult['data']['categories'];
        }
        $modules = [];
        if (!empty($remoteModuleResult['data']['modules'])) {
            foreach ($remoteModuleResult['data']['modules'] as $remote) {
                $remote['_isLocal'] = false;
                $remote['_isInstalled'] = false;
                $remote['_isEnabled'] = false;
                $remote['_localVersion'] = null;
                $remote['_isSystem'] = false;
                $remote['_hasConfig'] = false;
                $modules[$remote['name']] = $remote;
            }
        }
        foreach (ModuleManager::listModules() as $m => $config) {
            $info = ModuleManager::getModuleBasic($m);
            if (isset($modules[$m])) {
                $modules[$m]['_isInstalled'] = $config['isInstalled'];
                $modules[$m]['_isEnabled'] = $config['enable'];
                $modules[$m]['_localVersion'] = $info['version'];
                $modules[$m]['_isSystem'] = $config['isSystem'];
                $modules[$m]['_hasConfig'] = !empty($info['config']);
            } else {
                $modules[$m] = [
                    'id' => 0,
                    'name' => $m,
                    'title' => $info['title'],
                    'cover' => null,
                    'categoryId' => null,
                    'latestVersion' => $info['version'],
                    'releases' => [],
                    'url' => null,
                    'isFee' => false,
                    'priceSuper' => null,
                    'priceSuperEnable' => false,
                    'priceYear' => null,
                    'priceYearEnable' => false,
                    'description' => $info['description'],
                    '_isLocal' => true,
                    '_isInstalled' => $config['isInstalled'],
                    '_isEnabled' => $config['enable'],
                    '_localVersion' => $info['version'],
                    '_isSystem' => $config['isSystem'],
                    '_hasConfig' => !empty($info['config']),
                ];
            }
        }
        return [
            'categories' => $categories,
            'modules' => array_values($modules),
        ];
    }

    private static function baseRequest($api, $data, $token)
    {
        return CurlUtil::postJSONBody(self::REMOTE_BASE . $api, $data, [
            'header' => [
                'api-token' => $token,
                'X-Requested-With' => 'XMLHttpRequest',
            ]
        ]);
    }

    public static function downloadPackage($token, $module, $version)
    {
        $ret = self::baseRequest('/api/store/module_package', [
            'module' => $module,
            'version' => $version,
        ], $token);
        BizException::throwsIfResponseError($ret);
        $package = $ret['data']['package'];
        $packageMd5 = $ret['data']['packageMd5'];
        $licenseKey = $ret['data']['licenseKey'];
        $data = CurlUtil::getRaw($package);
        BizException::throwsIfEmpty('安装包获取失败', $data);
        $zipTemp = FileUtil::generateLocalTempPath('zip');
        file_put_contents($zipTemp, $data);
        BizException::throwsIf('文件MD5校验失败', md5_file($zipTemp) != $packageMd5);
        return Response::generateSuccessData([
            'package' => $zipTemp,
            'licenseKey' => $licenseKey,
            'packageSize' => filesize($zipTemp),
        ]);
    }

    public static function unpackModule($module, $package, $licenseKey)
    {
        BizException::throwsIf('文件不存在 ' . $package, empty($package) || !file_exists($package));
        $moduleDir = base_path('module/' . $module);
        if (file_exists($moduleDir)) {
            BizException::throwsIf('模块目录 module/' . $module . ' 不正常，请手动删除', !is_dir($moduleDir));
            $backupDir = base_path('module/_delete_.' . date('Ymd_His') . '.' . $module);
            rename($moduleDir, $backupDir);
            BizException::throwsIf('备份模块旧文件失败', !file_exists($backupDir));
        }
        BizException::throwsIf('模块目录 module/' . $module . ' 不正常，请手动删除', file_exists($moduleDir));
        $zipper = new Zipper();
        $zipper->make($package);
        if ($zipper->contains($module . '/config.json')) {
            $zipper->folder($module . '');
        }
        $zipper->extractTo($moduleDir);
        $zipper->close();
        BizException::throwsIf('解压失败', !file_exists($moduleDir . '/config.json'));
        file_put_contents($moduleDir . '/license.json', json_encode([
            'licenseKey' => $licenseKey,
        ]));
        @unlink($package);
        return Response::generateSuccessData([]);
    }

    public static function removeModule($module, $version)
    {
        $moduleDir = base_path('module/' . $module);
        BizException::throwsIf('模块目录不存在 ', !file_exists($moduleDir));
        BizException::throwsIf('模块目录 module/' . $module . ' 不正常，请手动删除', !is_dir($moduleDir));
        $backupDir = base_path('module/_delete_.' . date('Ymd_His') . '.' . $module);
        rename($moduleDir, $backupDir);
        BizException::throwsIf('模块目录备份失败', !file_exists($backupDir));
        return Response::generateSuccessData([]);
    }

}
