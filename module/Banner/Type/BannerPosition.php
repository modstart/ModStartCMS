<?php


namespace Module\Banner\Type;


use ModStart\Core\Type\BaseType;
use ModStart\Module\ModuleManager;
use Module\Banner\Biz\BannerPositionBiz;
use Module\Banner\Provider\BannerPositionProvider;

class BannerPosition implements BaseType
{
    public static function getList()
    {
        return array_merge(
            ModuleManager::getModuleConfigKeyValueItems('Banner', 'position'),
            BannerPositionProvider::allMap(),
            BannerPositionBiz::allMap()
        );
    }
}
