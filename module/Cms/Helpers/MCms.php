<?php

use Illuminate\Support\Facades\DB;
use Module\Cms\Util\CmsCatUtil;
use Module\Cms\Util\CmsContentUtil;
use Module\Cms\Util\CmsMemberPermitUtil;

/**
 * @Util CMS操作
 * @remark
 * 1 统一用单数
 * 2 分页统一使用 page
 * @Placeholder@CmsCat
 * {
 *   "id": 2,
 *   "created_at": "2021-11-03 16:38:21",
 *   "updated_at": "2022-04-13 09:34:23",
 *   "title": "行业资讯",
 *   "subTitle": null,
 *   "bannerBg": null,
 *   "url": "news/kind1",
 *   "seoTitle": null,
 *   "seoDescription": null,
 *   "seoKeywords": null,
 *   "icon": null,
 *   "cover": "http://example.com/cover.jpg",
 *   "_url": "/news/kind1"
 * }
 * @Placeholder@CmsContent
 * {
 *   "id": 2,
 *   "created_at": "2021-11-03 18:54:05",
 *   "updated_at": "2023-11-13 15:30:33",
 *   "catId": 10,
 *   "modelId": 4,
 *   "title": "标题",
 *   "summary": "摘要",
 *   "cover": "https://example.com/cover.jpg",
 *   "seoTitle": null,
 *   "seoDescription": null,
 *   "seoKeywords": null,
 *   "postTime": "2021-11-03 18:52:14",
 *   "wordCount": null,
 *   "viewCount": 388,
 *   "commentCount": null,
 *   "likeCount": null,
 *   "isRecommend": 1,
 *   "isTop": 1,
 *   "author": "作者",
 *   "source": "来源",
 *   "_url": "/news/2",
 *   "_tags": [
 *     "标签"
 *   ],
 *   "_tagList": [
 *     {
 *       "name": "标签",
 *       "url": "/tag/标签"
 *     }
 *   ],
 *   "_data": {
 *     "content": "富文本内容",
 *     "xxx": "自定义字段"
 *   }
 * }
 * @Placeholder@CmsPageContentOption
 * // 简单条件（字段限定在主表记录）
 * $option = [ 'where'=>[ 'title'=>'新闻' ] ];
 * // 模糊搜索（字段限定在主表记录）
 * $option = [ 'whereOperate'=>[ 'title', 'like', '%关键词%' ] ];
 * // 副表关联搜索（关联副表 cms_m_xxx，同时搜索副表字段 title = 标题 的记录）
 * $option = [ 'fieldFilterTable'=>'cms_m_xxx', 'fieldFilter'=>[ [ 'condition'=>'is', 'field'=>'title', 'value'=>'标题' ] ] ];
 */
class MCms
{
    /**
     * @deprecated delete after 20230928
     */
    public static function get($id)
    {
        $m = self::getContent($id);

    }

    /**
     * @Util 获取内容
     * @param $id int 内容ID
     * @return array
     * @returnExample
     * [Placeholder.CmsContent]
     */
    public static function getContent($id)
    {
        $m = CmsContentUtil::get($id);
        if (empty($m)) {
            return null;
        }
        return $m['record'];
    }

    /**
     * @Util 获取内容副表字段
     * @param $record array 主表记录
     * @return array
     * @returnExample
     * {
     *   "id": 2,
     *   "created_at": "2021-11-03 18:54:05",
     *   "updated_at": "2021-11-03 19:12:59",
     *   "content": "内容",
     *   "xxx": "自定义字段"
     * }
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
     * @Util 获取内容副表单个字段
     * @desc 该方法会在内存中缓存副表数据以提高性能
     * @param $record  array 主表记录
     * @param $fieldName string 字段名
     * @param $default mixed 默认值
     * @return string|number
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
     * @Util 获取栏目
     * @param $catId integer 栏目ID
     * @return array
     * @returnExample
     * [Placeholder.CmsCat]
     */
    public static function getCat($catId)
    {
        return CmsCatUtil::get($catId);
    }

    /**
     * @Util 获取栏目（根据URL）
     * @param $catUrl string 栏目URL
     * @return array
     * @returnExample
     * [Placeholder.CmsCat]
     */
    public static function getCatByUrl($catUrl)
    {
        return CmsCatUtil::getByUrl($catUrl);
    }

    /**
     * @Util 获取子栏目
     * @param $catId integer 栏目ID
     * @return array
     * @returnExample
     * [
     *   [Placeholder.CmsCat|indent:2,trim],
     *   ...
     * ]
     */
    public static function listCatChildren($catId)
    {
        return CmsCatUtil::children($catId);
    }

    /**
     * @Util 获取子栏目（根据URL）
     * @param $catUrl string 栏目URL
     * @return array
     * @returnExample
     * [
     *   [Placeholder.CmsCat|indent:2,trim],
     *   ...
     * ]
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
     * @deprecated delete after 20230938
     */
    public static function paginateChildrenCat($catId)
    {
        return self::listCatChildren($catId);
    }

    /**
     * @Util 获取栏目内容分页（含副表字段）
     * @desc 根据单个栏目ID获取内容分页，不包含子栏目，多个栏目必须为相同的模型
     * @param $catId int 栏目ID
     * @param $page int 页码
     * @param $pageSize int 分页大小
     * @param $option array 其他选项
     * @paramExample
     * [Placeholder.CmsPageContentOption]
     * @return array
     * @returnExample
     * {
     *   "total": 3,
     *   "records": [
     *     [Placeholder.CmsContent|indent:4,trim],
     *     ...
     *   ]
     * }
     * @example
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
     * @Util 获取栏目内容分页（含副表字段）
     * @desc 根据多个栏目ID获取内容分页，不包含子栏目，多个栏目必须为相同的模型
     * @param $catIds int[] 栏目ID
     * @param $page int 页码
     * @param $pageSize int 分页大小
     * @param $option array 其他选项
     * @paramExample
     * [Placeholder.CmsPageContentOption]
     * @return array
     * @returnExample
     * {
     *   "total": 3,
     *   "records": [
     *     [Placeholder.CmsContent|indent:4,trim],
     *     ...
     *   ]
     * }
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
     * @Util 获取栏目内容分页（含副表字段）
     * @desc 根据单个栏目URL获取内容分页，不包含子栏目，多个栏目必须为相同的模型
     * @param $catUrl string 栏目URL
     * @param $page int 页码
     * @param $pageSize int 分页大小
     * @param $option array 其他选项
     * @paramExample
     * [Placeholder.CmsPageContentOption]
     * @return array
     * @returnExample
     * {
     *   "total": 3,
     *   "records": [
     *     [Placeholder.CmsContent|indent:4,trim],
     *     ...
     *   ]
     * }
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
     * @Util 获取栏目内容分页（含副表字段）
     * @desc 根据多个栏目URL获取内容分页，不包含子栏目，多个栏目必须为相同的模型
     * @param $catUrls string[] 栏目URL
     * @param $page int 页码
     * @param $pageSize int 分页大小
     * @param $option array 其他选项
     * @paramExample
     * [Placeholder.CmsPageContentOption]
     * @return array
     * @returnExample
     * {
     *   "total": 3,
     *   "records": [
     *     [Placeholder.CmsContent|indent:4,trim],
     *     ...
     *   ]
     * }
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
     * @deprecated delete after 20240529
     */
    public static function listContentByCat($catId, $page = 1, $pageSize = 10, $option = [])
    {
        $paginateData = self::pageCat($catId, $page, $pageSize, $option);
        return $paginateData['records'];
    }

    /**
     * @deprecated delete after 20240529
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
     * @Util 获取栏目内容分页
     * @desc 根据单个栏目ID获取内容分页，不包含子栏目，多个栏目必须为相同的模型
     * @param $catUrl string 栏目URL
     * @param $page int 页码
     * @param $pageSize int 分页大小
     * @param $option array 其他选项
     * @paramExample
     * [Placeholder.CmsPageContentOption]
     * @return array
     * @returnExample
     * {
     *   "total": 3,
     *   "records": [
     *     [Placeholder.CmsContent|indent:4,trim],
     *     ...
     *   ]
     * }
     */
    public static function pageContentByCat($catId, $page = 1, $pageSize = 10, $option = [])
    {
        return CmsContentUtil::paginateCat($catId, $page, $pageSize, $option);
    }

    /**
     * @Util 获取栏目内容分页（根据URL）
     * @desc 根据单个栏目URL获取内容分页，不包含子栏目，多个栏目必须为相同的模型
     * @param $catUrl string 栏目URL
     * @param $page int 页码
     * @param $pageSize int 分页大小
     * @param $option array 其他选项
     * @paramExample
     * [Placeholder.CmsPageContentOption]
     * @return array
     * @returnExample
     * {
     *   "total": 3,
     *   "records": [
     *     [Placeholder.CmsContent|indent:4,trim],
     *     ...
     *   ]
     * }
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
     * @deprecated delete after 20230938
     */
    public static function pageCat($catId, $page = 1, $pageSize = 10, $option = [])
    {
        return self::pageContentByCat($catId, $page, $pageSize, $option);
    }

    /**
     * @Util 获取栏目最近内容列表
     * @param $catId int 栏目ID
     * @param $limit int 数量
     * @return array
     * @returnExample
     * [
     *   [Placeholder.CmsContent|indent:2,trim],
     *   ...
     * ]
     */
    public static function latestContentByCat($catId, $limit = 10)
    {
        $paginateData = CmsContentUtil::paginateCat($catId, 1, $limit);
        $records = $paginateData['records'];
        return $records;
    }

    /**
     * @Util 获取栏目最近内容列表（根据URL）
     * @param $cateUrl string 栏目URL
     * @param $limit int 数量
     * @returnExample
     * [
     *   [Placeholder.CmsContent|indent:2,trim],
     *   ...
     * ]
     */
    public static function latestContentByCatUrl($cateUrl, $limit = 10)
    {
        $cat = self::getCatByUrl($cateUrl);
        return self::latestContentByCat($cat['id'], $limit);
    }

    /**
     * @Util 获取栏目最近推荐内容列表
     * @param $catId int 栏目ID
     * @param $limit int 数量
     * @return array
     * @returnExample
     * [
     *   [Placeholder.CmsContent|indent:2,trim],
     *   ...
     * ]
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
     * @Util 获取栏目最近推荐内容列表（根据URL）
     * @param $cateUrl string 栏目URL
     * @param $limit int 数量
     * @returnExample
     * [
     *   [Placeholder.CmsContent|indent:2,trim],
     *   ...
     * ]
     */
    public static function latestRecommendContentByCatUrl($cateUrl, $limit = 10)
    {
        $cat = self::getCatByUrl($cateUrl);
        return self::latestRecommendContentByCat($cat['id'], $limit);
    }

    /**
     * @Util 获取栏目随机内容列表
     * @param $catId int 栏目ID
     * @param $limit int 数量
     * @return array
     * @returnExample
     * [
     *   [Placeholder.CmsContent|indent:2,trim],
     *   ...
     * ]
     */
    public static function randomContentByCat($catId, $limit = 10)
    {
        $option = [
            'order' => [DB::raw('RAND()'), ''],
        ];
        $paginateData = CmsContentUtil::paginateCat($catId, 1, $limit, $option);
        $records = $paginateData['records'];
        return $records;
    }

    /**
     * @Util 获取栏目置顶内容列表
     * @param $catId int 栏目ID
     * @param $limit int 数量
     * @return array
     * @returnExample
     * [
     *   [Placeholder.CmsContent|indent:2,trim],
     *   ...
     * ]
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
     * @Util 获取栏目置顶内容列表（根据URL）
     * @param $cateUrl string 栏目URL
     * @param $limit int 数量
     * @return array
     * @returnExample
     * [
     *   [Placeholder.CmsContent|indent:2,trim],
     *   ...
     * ]
     */
    public static function latestTopContentByCatUrl($cateUrl, $limit = 10)
    {
        $cat = self::getCatByUrl($cateUrl);
        return self::latestTopContentByCat($cat['id'], $limit);
    }

    /**
     * @deprecated delete after 20230928
     */
    public static function latestCat($catId, $limit = 10)
    {
        return self::latestContentByCat($catId, $limit);
    }

    /**
     * @Util 获取下一条内容
     * @param $catId int 栏目ID
     * @param $recordId int 记录ID
     * @return array|null
     * @returnExample
     * [Placeholder.CmsContent]
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
     * @Util 获取上一条内容
     * @param $catId int 栏目ID
     * @param $recordId int 记录ID
     * @return array|null
     * @returnExample
     * [Placeholder.CmsContent]
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
     * @Util 判断当前用户是否可以访问该栏目
     * @param $cat array 栏目
     * @return bool
     */
    public static function canVisitCat($cat)
    {
        return CmsMemberPermitUtil::canVisitCat($cat);
    }

    /**
     * @Util 判断当前用户是否可以发布到该栏目
     * @param $cat array
     * @return bool
     */
    public static function canPostCat($cat)
    {
        return CmsMemberPermitUtil::canPostCat($cat);
    }

}
