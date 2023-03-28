<?php

use Module\Cms\Util\CmsCatUtil;
use Module\Cms\Util\CmsContentUtil;
use Module\Cms\Util\CmsMemberPermitUtil;

/**
 * @Util CMS操作
 */
class MCms
{
    /**
     * @deprecated delete after 20230928
     */
    public static function get($id)
    {
        return self::getContent($id);
    }

    /**
     * @Util 获取内容
     * @param $id int 内容ID
     */
    public static function getContent($id)
    {
        return CmsContentUtil::get($id);
    }

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
    public static function listCatChildrenByUrl($catUrl)
    {
        $cat = CmsCatUtil::getByUrl($catUrl);
        return self::listCatChildren($cat['id']);
    }

    /**
     * @deprecated delete after 20230938
     */
    public static function paginateChildrenCatByUrl($catUrl)
    {
        return self::listCatChildrenByUrl($catUrl);
    }

    /**
     * @param $catId integer 栏目ID
     * @return array
     *
     * @Util 根据栏目ID获取子栏目
     */
    public static function listCatChildren($catId)
    {
        return CmsCatUtil::children($catId);
    }

    /**
     * @deprecated delete after 20230938
     */
    public static function paginateChildrenCat($catId)
    {
        return self::listCatChildren($catId);
    }

    /**
     * @param $catId int 栏目ID
     * @param $page int 页码
     * @param $pageSize int 分页大小
     * @param $option array 其他选项
     * @return array
     *
     * @Util 根据栏目ID获取内容列表（包含副表字段），不包含子栏目
     */
    public static function pageContentWithDataByCat($catId, $page = 1, $pageSize = 10, $option = [])
    {
        $cat = CmsCatUtil::get($catId);
        if (empty($cat)) {
            return [
                'total' => 0,
                'records' => [],
            ];
        }
        return CmsContentUtil::paginateCatsWithData([$cat], $page, $pageSize, $option);
    }

    /**
     * @deprecated delete after 20230938
     */
    public static function pageCatWithData($catId, $page = 1, $pageSize = 10, $option = [])
    {
        return self::pageContentWithDataByCat($catId, $page, $pageSize, $option);
    }

    /**
     * @param $catIds int[] 栏目ID
     * @param $page int 页码
     * @param $pageSize int 分页大小
     * @param $option array 其他选项
     * @return array
     *
     * @Util 根据多个栏目ID获取内容列表（包含副表字段），不包含子栏目，多个栏目必须为相同的模型
     */
    public static function pageContentWithDataByCats($catIds, $page = 1, $pageSize = 10, $option = [])
    {
        $cats = array_values(array_filter(array_map(function ($o) {
            return CmsCatUtil::get($o);
        }, $catIds)));
        if (empty($cats)) {
            return [
                'total' => 0,
                'records' => [],
            ];
        }
        return CmsContentUtil::paginateCatsWithData($cats, $page, $pageSize, $option);
    }

    /**
     * @deprecated delete after 20230938
     */
    public static function pageCatsWithData($catIds, $page = 1, $pageSize = 10, $option = [])
    {
        return self::pageContentWithDataByCats($catIds, $page, $pageSize, $option);
    }

    /**
     * @param $catUrl string 栏目URL
     * @param $page int 页码
     * @param $pageSize int 分页大小
     * @param $option array 其他选项
     * @return array
     *
     * @Util 根据栏目URL获取内容列表（包含副表字段），不包含子栏目
     */
    public static function pageContentWithDataByCatUrl($catUrl, $page = 1, $pageSize = 10, $option = [])
    {
        $cat = CmsCatUtil::getByUrl($catUrl);
        if (empty($cat)) {
            return [
                'total' => 0,
                'records' => [],
            ];
        }
        return CmsContentUtil::paginateCatsWithData([$cat], $page, $pageSize, $option);
    }

    /**
     * @deprecated delete after 20230938
     */
    public static function pageCatWithDataByUrl($catUrl, $page = 1, $pageSize = 10, $option = [])
    {
        return self::pageContentWithDataByCatUrl($catUrl, $page, $pageSize, $option);
    }

    /**
     * @param $catUrls string[] 栏目URL
     * @param $page int 页码
     * @param $pageSize int 分页大小
     * @param $option array 其他选项
     * @return array
     *
     * @Util 根据多个栏目URL获取列表（包含副表字段），不包含子栏目，多个栏目必须为相同的模型
     */
    public static function pageContentWithDataByCatsUrl($catUrls, $page = 1, $pageSize = 10, $option = [])
    {
        $cats = array_values(array_filter(array_map(function ($o) {
            return CmsCatUtil::getByUrl($o);
        }, $catUrls)));
        if (empty($cats)) {
            return [
                'total' => 0,
                'records' => [],
            ];
        }
        return CmsContentUtil::paginateCatsWithData($cats, $page, $pageSize, $option);
    }

    /**
     * @deprecated delete after 20230938
     */
    public static function pageCatsWithDataByUrl($catUrls, $page = 1, $pageSize = 10, $option = [])
    {
        return self::pageContentWithDataByCatsUrl($catUrls, $page, $pageSize, $option);
    }

    /**
     * @param $catUrl string 栏目URL
     * @param $page int 页码
     * @param $pageSize int 分页大小
     * @param $option array 其他选项
     * @return array
     *
     * @Util 根据栏目URL获取内容列表（不包含副表字段），包含子栏目
     */
    public static function listContentByCatUrl($catUrl, $page = 1, $pageSize = 10, $option = [])
    {
        $paginateData = self::pageCatByUrl($catUrl, $page, $pageSize, $option);
        return $paginateData['records'];
    }

    /**
     * @deprecated delete after 20230938
     */
    public static function listCatByUrl($catUrl, $page = 1, $pageSize = 10, $option = [])
    {
        return self::listContentByCatUrl($catUrl, $page, $pageSize, $option);
    }

    /**
     * @deprecated delete after 20230938
     */
    public static function paginateCatByUrl($catUrl, $page = 1, $pageSize = 10, $option = [])
    {
        return self::listContentByCatUrl($catUrl, $page, $pageSize, $option);
    }

    /**
     * @param $catId int 栏目ID
     * @param $page int 页码
     * @param $pageSize int 分页大小
     * @param $option array 其他选项
     * @return array
     *
     * @Util 根据栏目ID获取内容列表（不包含副表字段），包含子栏目
     */
    public static function listContentByCat($catId, $page = 1, $pageSize = 10, $option = [])
    {
        $paginateData = self::pageCat($catId, $page, $pageSize, $option);
        return $paginateData['records'];
    }

    /**
     * @deprecated delete after 20230938
     */
    public static function listCat($catId, $page = 1, $pageSize = 10, $option = [])
    {
        return self::listContentByCat($catId, $page, $pageSize, $option);
    }

    /**
     * @deprecated delete after 20230938
     */
    public static function paginateCat($catId, $page = 1, $pageSize = 10, $option = [])
    {
        return self::listContentByCat($catId, $page, $pageSize, $option);
    }

    /**
     * @param $catUrl string 栏目URL
     * @param $page int 页码
     * @param $pageSize int 分页大小
     * @param $option array 其他选项
     * @return array
     *
     * @Util 根据栏目URL获取内容列表（不包含副表字段），包含子栏目
     */
    public static function pageContentByCatUrl($catUrl, $page = 1, $pageSize = 10, $option = [])
    {
        $cat = CmsCatUtil::getByUrl($catUrl);
        if (empty($cat)) {
            return [
                'total' => 0,
                'records' => [],
            ];
        }
        return CmsContentUtil::paginateCat($cat['id'], $page, $pageSize, $option);
    }

    /**
     * @deprecated delete after 20230938
     */
    public static function pageCatByUrl($catUrl, $page = 1, $pageSize = 10, $option = [])
    {
        return self::pageContentByCatUrl($catUrl, $page, $pageSize, $option);
    }

    /**
     * @param $catId int 栏目ID
     * @param $page int 页码
     * @param $pageSize int 分页大小
     * @param $option array 其他选项
     * @return array
     *
     * @Util 根据栏目ID获取内容列表（不包含副表字段），包含子栏目
     */
    public static function pageContentByCat($catId, $page = 1, $pageSize = 10, $option = [])
    {
        return CmsContentUtil::paginateCat($catId, $page, $pageSize, $option);
    }

    /**
     * @deprecated delete after 20230938
     */
    public static function pageCat($catId, $page = 1, $pageSize = 10, $option = [])
    {
        return self::pageContentByCat($catId, $page, $pageSize, $option);
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
        return self::latestContentByCat($cat['id'], $limit);
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

    /**
     * @param $cateUrl string 栏目URL
     * @param $limit int 数量
     *
     * @Util 根据栏目URL获取最近置顶记录
     */
    public static function latestTopContentByCatUrl($cateUrl, $limit = 10)
    {
        $cat = self::getCatByUrl($cateUrl);
        return self::latestTopContentByCat($cat['id'], $limit);
    }

    /**
     * @param $catId int 栏目ID
     * @param $limit int 数量
     * @return array
     *
     * @Util 根据栏目ID获取最近置顶记录
     */
    public static function latestTopContentByCat($catId, $limit = 10)
    {
        $option = [
            'where' => [
                'isTop' => true,
            ]
        ];
        $paginateData = CmsContentUtil::paginateCat($catId, 1, $limit, $option);
        $records = $paginateData['records'];
        return $records;
    }

    /**
     * @deprecated delete after 20230928
     */
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
    public static function nextContent($catId, $recordId)
    {
        return CmsContentUtil::nextOne($catId, $recordId);
    }

    /**
     * @deprecated delete after 20230938
     */
    public static function nextOne($catId, $recordId)
    {
        return self::nextContent($catId, $recordId);
    }

    /**
     * @param $catId int 栏目ID
     * @param $recordId int 记录ID
     * @return array|null
     *
     * @Util 获取上一条记录
     */
    public static function prevContent($catId, $recordId)
    {
        return CmsContentUtil::prevOne($catId, $recordId);
    }

    /**
     * @deprecated delete after 20230938
     */
    public static function prevOne($catId, $recordId)
    {
        return self::prevContent($catId, $recordId);
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

}
