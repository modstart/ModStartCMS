<?php


namespace Module\Article\Type;

use ModStart\Core\Type\BaseType;
use ModStart\Module\ModuleManager;
use Module\Article\Biz\ArticlePositionBiz;

class ArticlePosition implements BaseType
{
    public static function getList()
    {
        return array_merge(
            ModuleManager::getModuleConfigKeyValueItems('Article', 'position'),
            ArticlePositionBiz::allMap()
        );
    }
}
