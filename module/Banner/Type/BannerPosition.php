<?php


namespace Module\Banner\Type;


use ModStart\Core\Type\BaseType;
use ModStart\Module\ModuleManager;

class BannerPosition implements BaseType
{
    public static function getList()
    {
        return ModuleManager::getModuleConfigKeyValueItems('Banner', 'position');
    }
}