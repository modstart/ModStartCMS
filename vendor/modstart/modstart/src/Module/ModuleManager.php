<?php


namespace ModStart\Module;

use Illuminate\Support\Facades\Artisan;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\ArrayUtil;
use ModStart\Core\Util\FileUtil;
use ModStart\Core\Util\VersionUtil;

class ModuleManager
{
    const MODULE_ENABLE_LIST = 'ModuleList';

    /**
     * 获取模块的基本信息
     * @param $name
     * @return array|null
     */
    public static function getModuleBasic($name)
    {
        if (file_exists($path = self::path($name, 'config.json'))) {
            $config = json_decode(file_get_contents($path), true);
            return array_merge([
                'name' => 'None',
                'title' => 'None',
                'version' => '1.0.0',
                'require' => [
                    // 'Xxx:*'
                    // 'Xxx:>=*'
                    // 'Xxx:==*'
                    // 'Xxx:<=*'
                    // 'Xxx:>*'
                    // 'Xxx:<*'
                ],
                'modstartVersion' => '*',
                'author' => 'Author',
                'description' => 'Description',
                'config' => [],
                'providers' => [],
            ], $config);
        }
        return null;
    }

    private static function callCommand($command, $param = [])
    {
        try {
            $exitCode = Artisan::call($command, $param);
            $output = trim(Artisan::output());
            if (0 !== $exitCode) {
                return Response::generate(-1, "ERROR:$exitCode", ['output' => $output]);
            }
            return Response::generateSuccessData(['output' => $output]);
        } catch (BizException $e) {
            return Response::generateError($e->getMessage());
        }
    }

    public static function clean($module)
    {
        $path = self::path($module);
        if (file_exists($path)) {
            FileUtil::rm($path, true);
        }
    }

    /**
     * 模块安装
     *
     * @param $module
     * @return array
     */
    public static function install($module, $force = false)
    {
        $param = ['module' => $module];
        if ($force) {
            $param['--force'] = true;
        }
        return self::callCommand('modstart:module-install', $param);
    }

    /**
     * 模块卸载
     * @param $module
     * @return array
     */
    public static function uninstall($module)
    {
        return self::callCommand('modstart:module-uninstall', ['module' => $module]);
    }

    /**
     * 模块启用
     * @param $module
     * @return array
     */
    public static function enable($module)
    {
        return self::callCommand('modstart:module-enable', ['module' => $module]);
    }

    /**
     * 模块禁用
     * @param $module
     * @return array
     */
    public static function disable($module)
    {
        return self::callCommand('modstart:module-disable', ['module' => $module]);
    }

    /**
     * 检查模块是否存在
     * @param $name
     * @return bool
     */
    public static function isExists($name)
    {
        return file_exists(self::path($name, 'config.json'));
    }

    /**
     * 模块绝对路径
     * @param $module
     * @param string $path
     * @return string
     */
    public static function path($module, $path = '')
    {
        return base_path(self::relativePath($module, $path));
    }

    /**
     * 模块相对路径
     * @param $module
     * @param string $path
     * @return string
     */
    public static function relativePath($module, $path = '')
    {
        return "module/$module" . ($path ? "/" . trim($path, '/') : '');
    }

    /**
     * 检测是否是系统模块
     * @param $name
     * @return bool
     */
    public static function isSystemModule($module)
    {
        $modules = config('module.system', []);
        return isset($modules[$module]);
    }

    /**
     * 检测模块是否已安装
     * @param $name
     * @return bool
     */
    public static function isModuleInstalled($name)
    {
        if (!self::isExists($name)) {
            return false;
        }
        $modules = self::listAllInstalledModules();
        return isset($modules[$name]);
    }

    /**
     * 检测模块是否启用
     * @param $name
     * @return bool
     */
    public static function isModuleEnabled($name)
    {
        $modules = self::listAllInstalledModules();
        return !empty($modules[$name]['enable']);
    }

    /**
     * 列出本地所有的模块
     * @return array
     */
    public static function listModules()
    {
        $files = FileUtil::listFiles(base_path('module'));
        $modules = [];
        foreach ($files as $v) {
            if (!$v['isDir']) {
                continue;
            }
            if (starts_with($v['filename'], '_delete_.')) {
                continue;
            }
            $modules[$v['filename']] = [
                'enable' => false,
                'isSystem' => false,
                'isInstalled' => false,
                'config' => [],
            ];
        }
        foreach (self::listSystemInstalledModules() as $m => $config) {
            if (isset($modules[$m])) {
                $modules[$m]['isInstalled'] = true;
                $modules[$m]['isSystem'] = true;
                $modules[$m]['enable'] = !empty($config['enable']);
            }
        }
        foreach (self::listUserInstalledModules() as $m => $config) {
            if (isset($modules[$m])) {
                $modules[$m]['isInstalled'] = true;
                $modules[$m]['enable'] = !empty($config['enable']);
            }
        }
        return $modules;
    }

    /**
     * 列出所有已安装系统模块
     * @return array
     */
    public static function listSystemInstalledModules()
    {
        $modules = array_build(config('module.system', []), function ($k, $v) {
            $v['isSystem'] = true;
            if (!isset($v['enable'])) {
                $v['enable'] = false;
            }
            return [$k, $v];
        });
        if (config('env.MS_MODULES')) {
            foreach (explode(',', config('env.MS_MODULES')) as $m) {
                if (!empty($m)) {
                    $modules[$m] = [
                        'enable' => true,
                    ];
                }
            }
        }
        return $modules;
    }

    /**
     * 列出所有已安装用户模块
     * @return array|mixed
     */
    public static function listUserInstalledModules()
    {
        try {
            return array_build(modstart_config()->getArray(self::MODULE_ENABLE_LIST), function ($k, $v) {
                $v['isSystem'] = false;
                if (!isset($v['enable'])) {
                    $v['enable'] = false;
                }
                return [$k, $v];
            });
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * 列出所有已安装模块，包括系统和用户安装
     * @return array
     */
    public static function listAllEnabledModules()
    {
        return array_filter(self::listAllInstalledModules(), function ($item) {
            return $item['enable'];
        });
    }

    /**
     * 列出所有模块，包括系统和用户安装
     * @return array|mixed
     */
    public static function listAllInstalledModules()
    {
        static $modules = null;
        if (null !== $modules) {
            return $modules;
        }
        $modules = array_merge(self::listUserInstalledModules(), self::listSystemInstalledModules());
        return $modules;
    }

    /**
     * 保存用户模块信息
     * @param $modules
     */
    public static function saveUserInstalledModules($modules)
    {
        $modules = array_map(function ($item) {
            return ArrayUtil::keepKeys($item, [
                'config', 'enable',
            ]);
        }, array_filter($modules, function ($m) {
            return empty($m['isSystem']);
        }));
        modstart_config()->setArray(self::MODULE_ENABLE_LIST, $modules);
    }

    /**
     * 获取已安装模块的依赖数
     * @param $ignoreError
     * @return array
     * @throws BizException
     */
    public static function listAllInstalledModulesInRequiredOrder($ignoreError = false)
    {
        $modules = self::listAllInstalledModules();
        $modules = array_keys($modules);
        $moduleInfoMap = [];
        foreach ($modules as $module) {
            $basic = self::getModuleBasic($module);
            if (empty($basic)) {
                continue;
            }
            $moduleInfoMap[$module] = $basic['require'];
        }
        $orderedModules = [];
        for ($i = 0; $i < 100; $i++) {
            foreach ($modules as $module) {
                if (in_array($module, $orderedModules)) {
                    continue;
                }
                $allPassed = true;
                if (!empty($moduleInfoMap[$module])) {
                    foreach ($moduleInfoMap[$module] as $requireModule) {
                        list($m, $v) = VersionUtil::parse($requireModule);
                        if (!in_array($m, $orderedModules)) {
                            $allPassed = false;
                        }
                    }
                }
                if ($allPassed) {
                    $orderedModules[] = $module;
                }
            }
            if (count($orderedModules) == count($modules)) {
                break;
            }
        }
        if (!$ignoreError) {
            if (count($modules) !== count($orderedModules)) {
                list($inserts, $deletes) = ArrayUtil::diff($orderedModules, $modules);
                $errors = [];
                foreach ($inserts as $insert) {
                    $requires = $moduleInfoMap[$insert];
                    foreach ($requires as $one) {
                        if (!in_array($one, $orderedModules)) {
                            $errors[] = "Module <$insert> Depends On <$one>";
                        }
                    }
                }
                if (!empty($errors)) {
                    BizException::throws('Module Not Fully Installed! ' . join('; ', $errors));
                } else {
                    BizException::throws('Module Not Fully Installed! requires ' . json_encode($modules));
                }
            }
        }
        return $orderedModules;
    }

    /**
     * 获取已安装模块信息
     * @param $module
     * @return mixed|null
     */
    public static function getInstalledModuleInfo($module)
    {
        $modules = self::listAllInstalledModules();
        return isset($modules[$module]) ? $modules[$module] : null;
    }

    /**
     * 保存模块设置
     * @param $module
     * @param $config
     */
    public static function saveUserInstalledModuleConfig($module, $config)
    {
        $modules = self::listUserInstalledModules();
        if (!empty($modules[$module])) {
            if (empty($modules[$module]['config'])) {
                $modules[$module]['config'] = [];
            }
            $modules[$module]['config'] = array_merge($modules[$module]['config'], $config);
        }
        self::saveUserInstalledModules($modules);
    }

    /**
     * 获取模块配置信息
     * @param $module
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    public static function getModuleConfig($module, $key, $default = null)
    {
        $moduleInfo = self::getInstalledModuleInfo($module);
        if (isset($moduleInfo['config'][$key])) {
            return $moduleInfo['config'][$key];
        }
        return $default;
    }

    public static function getModuleConfigArray($module, $key, $default = [])
    {
        $value = self::getModuleConfig($module, $key);
        if (is_array($value)) {
            return $value;
        }
        $value = @json_decode($value, true);
        if (empty($value)) {
            $value = $default;
        }
        return $value;
    }

    public static function getModuleConfigBoolean($module, $key, $default = false)
    {
        return !!self::getModuleConfig($module, $key, $default);
    }

    public static function getModuleConfigKeyValueItems($module, $key, $default = [])
    {
        $value = self::getModuleConfigArray($module, $key, $default);
        $result = [];
        if (!empty($value) && is_array($value)) {
            foreach ($value as $item) {
                if (isset($item['k']) && isset($item['v'])) {
                    $result[$item['k']] = $item['v'];
                }
            }
        }
        return $result;
    }

    public static function getModuleConfigKeyValueItem($module, $key, $itemKey, $default = null)
    {
        $items = self::getModuleConfigKeyValueItems($module, $key);
        if (isset($items[$itemKey])) {
            return $items[$itemKey];
        }
        return $default;
    }

}
