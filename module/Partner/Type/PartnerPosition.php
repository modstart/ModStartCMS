<?php


namespace Module\Partner\Type;


use ModStart\Core\Type\BaseType;
use ModStart\Module\ModuleManager;
use Module\Partner\Biz\PartnerPositionBiz;

class PartnerPosition implements BaseType
{
    public static function getList()
    {
        return array_merge(
            ModuleManager::getModuleConfigKeyValueItems('Partner', 'position'),
            PartnerPositionBiz::allMap()
        );
    }
}
