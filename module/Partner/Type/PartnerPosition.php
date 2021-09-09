<?php


namespace Module\Partner\Type;


use ModStart\Core\Type\BaseType;
use ModStart\Module\ModuleManager;

class PartnerPosition implements BaseType
{
    public static function getList()
    {
        return ModuleManager::getModuleConfigKeyValueItems('Partner', 'position');
    }
}