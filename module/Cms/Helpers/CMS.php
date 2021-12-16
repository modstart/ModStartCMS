<?php

/**
 * Class Cms
 *
 * @Util CMS操作
 */
class Cms
{
    /**
     * @param $catUrl string 栏目URL
     * @param $page int 页码
     * @param $pageSize int 分页大小
     * @param $option array 其他选项
     * @return mixed
     *
     * @Util 根据栏目URL获取列表
     */
    public static function paginateCatByUrl($catUrl, $page, $pageSize, $option = [])
    {
        $cat = \Module\Cms\Util\CmsCatUtil::getByUrl($catUrl);
        $paginateData = \Module\Cms\Util\CmsContentUtil::paginateCat($cat['id'], $page, $pageSize, $option);
        return $paginateData['records'];
    }

    /**
     * @param $catId
     * @param $page
     * @param $pageSize
     * @param $option array
     * @return mixed
     *
     * @Util 根据栏目ID获取列表
     */
    public static function paginateCat($catId, $page, $pageSize, $option = [])
    {
        $paginateData = \Module\Cms\Util\CmsContentUtil::paginateCat($catId, $page, $pageSize, $option);
        return $paginateData['records'];
    }

    /**
     * @param $position
     * @return array|mixed
     *
     * @Util 根据位置获取轮播
     */
    public static function banners($position)
    {
        if (!\ModStart\Module\ModuleManager::isModuleEnabled('Banner')) {
            return [];
        }
        return \Module\Banner\Util\BannerUtil::listByPositionWithCache($position);
    }

    /**
     * @param $position
     * @return array|mixed
     *
     * @Util 根据位置获取合作伙伴
     */
    public static function partners($position)
    {
        if (!\ModStart\Module\ModuleManager::isModuleEnabled('Partner')) {
            return [];
        }
        return \Module\Partner\Util\PartnerUtil::listByPositionWithCache($position);
    }
}
