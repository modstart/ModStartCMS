<?php

use Module\Cms\Util\CmsCatUtil;
use Module\Cms\Util\CmsContentUtil;

/**
 * Class MCms
 *
 * @Util CMS操作
 */
class MCms
{
    /**
     * @param $catUrl string 栏目URL
     * @param $page int 页码
     * @param $pageSize int 分页大小
     * @param $option array 其他选项
     * @return array
     *
     * @Util 根据栏目URL获取列表
     */
    public static function paginateCatByUrl($catUrl, $page = 1, $pageSize = 10, $option = [])
    {
        $cat = CmsCatUtil::getByUrl($catUrl);
        $paginateData = CmsContentUtil::paginateCat($cat['id'], $page, $pageSize, $option);
        return $paginateData['records'];
    }

    /**
     * @param $catId int 栏目ID
     * @param $page int 页码
     * @param $pageSize int 分页大小
     * @param $option array 其他选项
     * @return array
     *
     * @Util 根据栏目ID获取列表
     */
    public static function paginateCat($catId, $page = 1, $pageSize = 10, $option = [])
    {
        $paginateData = CmsContentUtil::paginateCat($catId, $page, $pageSize, $option);
        return $paginateData['records'];
    }

    /**
     * @param $catId int 栏目ID
     * @param $limit int 页码
     * @return array
     *
     * @Util 根据栏目ID获取最近记录
     */
    public static function latestCat($catId, $limit = 10)
    {
        $paginateData = CmsContentUtil::paginateCat($catId, 1, $limit);
        $latestRecords = $paginateData['records'];
        return $latestRecords;
    }

    /**
     * @param $catId int 栏目ID
     * @param $recordId int 记录ID
     * @return array|null
     *
     * @Util 获取下一条记录
     */
    public static function nextOne($catId, $recordId)
    {
        return CmsContentUtil::nextOne($catId, $recordId);
    }

    /**
     * @param $catId int 栏目ID
     * @param $recordId int 记录ID
     * @return array|null
     *
     * @Util 获取上一条记录
     */
    public static function prevOne($catId, $recordId)
    {
        return CmsContentUtil::prevOne($catId, $recordId);
    }
}