<?php


namespace Module\Nav\Type;


use ModStart\Core\Type\BaseType;
use ModStart\Module\ModuleManager;

class NavPosition implements BaseType
{
    public static function getList()
    {
        return ModuleManager::getModuleConfigKeyValueItems('Nav', 'position');
    }

    public static function first()
    {
        $list = self::getList();
        $keys = array_keys($list);
        return array_shift($keys);
    }
}