<?php


namespace Module\AdminManager\Util;


use ModStart\Core\Util\MetaUtil;
use ModStart\ModStart;
use ModStart\Module\ModuleManager;

class ModuleUtil
{
    public static function modules()
    {
        $modules = [];
        $modules[] = "APP:" . MetaUtil::get('APP') . ':' . MetaUtil::get('VERSION');
        $modules[] = "ModStart:" . ModStart::$version;
        foreach (ModuleManager::listAllEnabledModules() as $m => $_) {
            $info = ModuleManager::getModuleBasic($m);
            if (!$info) {
                continue;
            }
            $modules[] = "$m:$info[version]";
        }
        return $modules;
    }
}
