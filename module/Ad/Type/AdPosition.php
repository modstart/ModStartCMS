<?php


namespace Module\Ad\Type;


use ModStart\Core\Type\BaseType;
use ModStart\Module\ModuleManager;

class AdPosition implements BaseType
{
    public static function getList()
    {
        return ModuleManager::getModuleConfigKeyValueItems('Ad', 'position');
    }
}