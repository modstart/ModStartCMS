<?php


namespace Module\Article\Type;


use ModStart\Core\Type\BaseType;
use ModStart\Module\ModuleManager;

class ArticlePosition implements BaseType
{
    public static function getList()
    {
        return ModuleManager::getModuleConfigKeyValueItems('Article', 'position');
    }
}