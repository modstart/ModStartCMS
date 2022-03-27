<?php


namespace Module\Nav\Type;


use ModStart\Core\Type\BaseType;
use ModStart\Module\ModuleManager;
use Module\Nav\Biz\NavPositionBiz;

class NavPosition implements BaseType
{
    public static function getList()
    {
        return array_merge(
            ModuleManager::getModuleConfigKeyValueItems('Nav', 'position'),
            NavPositionBiz::allMap()
        );
    }

    public static function first()
    {
        $list = self::getList();
        $keys = array_keys($list);
        return array_shift($keys);
    }
}
