<?php

class Cms
{
    public static function paginateCatByUrl($catUrl, $page, $pageSize, $option = [])
    {
        $cat = \Module\Cms\Util\CmsCatUtil::getByUrl($catUrl);
        $paginateData = \Module\Cms\Util\CmsContentUtil::paginateCat($cat['id'], $page, $pageSize, $option);
        return $paginateData['records'];
    }

    public static function paginateCat($catId, $page, $pageSize, $option = [])
    {
        $paginateData = \Module\Cms\Util\CmsContentUtil::paginateCat($catId, $page, $pageSize, $option);
        return $paginateData['records'];
    }

    public static function banners($position)
    {
        if (!\ModStart\Module\ModuleManager::isModuleEnabled('Banner')) {
            return [];
        }
        return \Module\Banner\Util\BannerUtil::listByPositionWithCache($position);
    }

    public static function partners($position)
    {
        if (!\ModStart\Module\ModuleManager::isModuleEnabled('Partner')) {
            return [];
        }
        return \Module\Partner\Util\PartnerUtil::listByPositionWithCache($position);
    }
}
