<?php


namespace Module\AdminManager\Util;


use App\Constant\AppConstant;
use Chumper\Zipper\Zipper;
use Illuminate\Support\Facades\Artisan;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\CurlUtil;
use ModStart\Core\Util\FileUtil;
use ModStart\ModStart;

class UpgradeUtil
{
    // const REMOTE_BASE = 'http://org.demo.soft.host';
    const REMOTE_BASE = 'https://modstart.com';

    private static function baseRequest($api, $data = [], $token = null)
    {
        return CurlUtil::postJSONBody(self::REMOTE_BASE . $api, $data, [
            'header' => [
                'api-token' => $token,
                'X-Requested-With' => 'XMLHttpRequest',
            ]
        ]);
    }

    public static function latest()
    {
        $ret = self::baseRequest('/api/app/latest', [
            'app' => AppConstant::APP,
            'version' => AppConstant::VERSION,
        ]);
        BizException::throwsIfResponseError($ret);
        return $ret['data'];
    }

    public static function downloadPackage($token, $app, $fromVersion, $toVersion)
    {
        $ret = self::baseRequest('/api/app/download_package', [
            'app' => $app,
            'fromVersion' => $fromVersion,
            'toVersion' => $toVersion,
        ], $token);
        BizException::throwsIfResponseError($ret);
        $package = $ret['data']['package'];
        $packageMd5 = $ret['data']['packageMd5'];
        $diffContent = $ret['data']['diffContent'];
        $data = CurlUtil::getRaw($package);
        BizException::throwsIfEmpty('安装包获取失败', $data);
        $zipTemp = FileUtil::generateLocalTempPath('zip');
        file_put_contents($zipTemp, $data);
        BizException::throwsIf('文件MD5校验失败', md5_file($zipTemp) != $packageMd5);
        $diffContentFile = FileUtil::generateLocalTempPath('json');
        file_put_contents($diffContentFile, $diffContent);
        return Response::generateSuccessData([
            'diffContentFile' => $diffContentFile,
            'package' => $zipTemp,
            'packageSize' => filesize($zipTemp),
        ]);
    }

    public static function upgradePackage($package, $diffContentFile)
    {
        BizException::throwsIf('package不存在', !file_exists($package));
        BizException::throwsIf('diffContentFile不存在', !file_exists($diffContentFile));
        $diffContent = @json_decode(file_get_contents($diffContentFile), true);
        BizException::throwsIf('diffContent为空', empty($diffContent));
        $checkFiles = [];
        if (!empty($diffContent['add'])) {
            $checkFiles = array_merge($checkFiles, $diffContent['add']);
        }
        if (!empty($diffContent['update'])) {
            $checkFiles = array_merge($checkFiles, $diffContent['update']);
        }
        $ret = FileUtil::filePathWritableCheck($checkFiles);
        BizException::throwsIfResponseError($ret);
        $zipper = new Zipper();
        $zipper->make($package);
        $logs = [];
        if (!empty($diffContent['add'])) {
            $logs[] = "新增文件：";
            foreach ($diffContent['add'] as $file) {
                $content = $zipper->getFileContent($file);
                FileUtil::write(base_path($file), $content);
                $logs[] = "> $file";
            }
            $logs[] = "";
        }
        if (!empty($diffContent['update'])) {
            $logs[] = "修改文件：";
            foreach ($diffContent['update'] as $file) {
                $content = $zipper->getFileContent($file);
                FileUtil::write(base_path($file), $content);
                $logs[] = "> $file";
            }
            $logs[] = "";
        }
        if (!empty($diffContent['delete'])) {
            $logs[] = "删除文件：";
            foreach ($diffContent['delete'] as $file) {
                if (file_exists($f = base_path($file))) {
                    @unlink($f);
                    $logs[] = "> $file";
                }
            }
            $logs[] = "";
        }
        $zipper->close();
        ModStart::clearCache();
        try {
            $exitCode = Artisan::call("migrate");
        } catch (\Exception $e) {
            $exitCode = -1;
        }
        BizException::throwsIf("调用 php artisan migrate 失败", 0 != $exitCode);
        $exitCode = Artisan::call("modstart:module-install-all");
        BizException::throwsIf("调用 php artisan modstart:module-install-all 失败", 0 != $exitCode);
        FileUtil::safeCleanLocalTemp($package);
        FileUtil::safeCleanLocalTemp($diffContentFile);
        return Response::generateSuccessData([
            'logs' => $logs,
        ]);
    }
}
