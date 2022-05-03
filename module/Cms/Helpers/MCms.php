<?php

use Module\Cms\Util\CmsCatUtil;
use Module\Cms\Util\CmsContentUtil;
use Module\Cms\Util\CmsMemberPermitUtil;

/**
 * Class MCms
 *
 * @Util CMS操作
 */
class MCms
{

    /**
     * @param $catUrl string 栏目URL
     * @return array
     *
     * @Util 获取栏目
     */
    public static function getCatByUrl($catUrl)
    {
        return CmsCatUtil::getByUrl($catUrl);
    }

    /**
     * @param $catId integer 栏目ID
     * @return array
     *
     * @Util 获取栏目
     */
    public static function getCat($catId)
    {
        return CmsCatUtil::get($catId);
    }

    /**
     * @param $catUrl string 栏目URL
     * @return array
     *
     * @Util 根据栏目URL获取子栏目
     */
    public static function listChildrenCatByUrl($catUrl)
    {
        $cat = CmsCatUtil::getByUrl($catUrl);
        return self::listChildrenCat($cat['id']);
    }

    /**
     * @param $catId integer 栏目ID
     * @return array
     *
     * @Util 根据栏目ID获取子栏目
     */
    public static function listChildrenCat($catId)
    {
        return CmsCatUtil::children($catId);
    }

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
        if (empty($cat)) {
            return [];
        }
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
     * @param $cateUrl string 栏目URL
     * @param $limit int 数量
     *
     * @Util 根据栏目URL获取最近记录
     */
    public static function latestContentByCatUrl($cateUrl, $limit = 10)
    {
        $cat = self::getCatByUrl($cateUrl);
        return self::latestCat($cat['id'], $limit);
    }

    /**
     * @param $catId int 栏目ID
     * @param $limit int 数量
     * @return array
     *
     * @Util 根据栏目ID获取最近记录
     */
    public static function latestContentByCat($catId, $limit = 10)
    {
        $paginateData = CmsContentUtil::paginateCat($catId, 1, $limit);
        $records = $paginateData['records'];
        return $records;
    }

    /**
     * @param $cateUrl string 栏目URL
     * @param $limit int 数量
     *
     * @Util 根据栏目URL获取最近推荐记录
     */
    public static function latestRecommendContentByCatUrl($cateUrl, $limit = 10)
    {
        $cat = self::getCatByUrl($cateUrl);
        return self::latestRecommendContentByCat($cat['id'], $limit);
    }

    /**
     * @param $catId int 栏目ID
     * @param $limit int 数量
     * @return array
     *
     * @Util 根据栏目ID获取最近推荐记录
     */
    public static function latestRecommendContentByCat($catId, $limit = 10)
    {
        $option = [
            'where' => [
                'isRecommend' => true,
            ]
        ];
        $paginateData = CmsContentUtil::paginateCat($catId, 1, $limit, $option);
        $records = $paginateData['records'];
        return $records;
    }

    public static function latestCat($catId, $limit = 10)
    {
        return self::latestContentByCat($catId, $limit);
    }

    /**
     * @param $record array 基础内容
     * @return array
     *
     * @Util 获取记录副表数据
     */
    public static function getContentData($record)
    {
        if (empty($record)) {
            return null;
        }
        $cat = self::getCat($record['catId']);
        return CmsContentUtil::getData($cat, $record['id']);
    }

    /**
     * @param $record  array 基础内容
     * @param $fieldName string 字段名
     * @param $default mixed 默认值
     * @return mixed
     *
     * @Util 获取记录副表数据
     */
    public static function getContentDataField($record, $fieldName, $default = null)
    {
        static $pool = [];
        if (empty($record)) {
            return null;
        }
        if (isset($pool[$record['id']])) {
            $data = $pool[$record['id']];
        } else {
            $data = self::getContentData($record);
            $pool[$record['id']] = $data;
        }
        return isset($data[$fieldName]) ? $data[$fieldName] : $default;
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


    /**
     * @param $cat array 栏目
     * @return bool
     *
     * @Util 判断是否可以访问栏目内容
     */
    public static function canVisitCat($cat)
    {
        return CmsMemberPermitUtil::canVisitCat($cat);
    }

    /**
     * @param $cat array
     * @return bool
     *
     * @Util 判断用户是否可以发布到该栏目
     */
    public static function canPostCat($cat)
    {
        return CmsMemberPermitUtil::canPostCat($cat);
    }


    public static function getCatTreeWithPost()
    {

    }
}
