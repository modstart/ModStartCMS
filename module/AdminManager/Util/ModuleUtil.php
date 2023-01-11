<?php


namespace Module\AdminManager\Util;


use ModStart\ModStart;
use ModStart\Module\ModuleManager;

class ModuleUtil
{
    public static function modules()
    {
        $modules = [];
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
