<?php


namespace Module\Banner\Type;


use ModStart\Core\Type\BaseType;
use ModStart\Module\ModuleManager;
use Module\Banner\Biz\BannerPositionBiz;

class BannerPosition implements BaseType
{
    public static function getList()
    {
        return array_merge(
            ModuleManager::getModuleConfigKeyValueItems('Banner', 'position'),
            BannerPositionBiz::allMap()
        );
    }
}
