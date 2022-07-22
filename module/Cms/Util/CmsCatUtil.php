<?php


namespace Module\Cms\Util;


use Illuminate\Support\Facades\Cache;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Util\ArrayUtil;
use ModStart\Core\Util\TreeUtil;
use Module\Cms\Type\CatUrlMode;

/**
 * @Util CMS栏目操作
 */
class CmsCatUtil
{
    /**
     * @Util 清除缓存
     */
    public static function clearCache()
    {
        Cache::forget('CmsCatAll');
        Cache::forget('CmsCatMap');
    }

    /**
     * @return array
     *
     * @Util 获取所有栏目
     */
    public static function all()
    {
        return Cache::rememberForever('CmsCatAll', function () {
            $records = ModelUtil::all('cms_cat', [
                'enable' => true,
            ]);
            ModelUtil::decodeRecordsNumberArray($records, [
                'visitMemberGroups', 'visitMemberVips',
                'postMemberGroups', 'postMemberVips',
            ]);
            foreach ($records as $k => $v) {
                $records[$k]['_model'] = CmsModelUtil::get($v['modelId']);
                $records[$k]['_url'] = CatUrlMode::url($v);
            }
            return $records;
        });
    }

    public static function map()
    {
        return Cache::rememberForever('CmsCatMap', function () {
            return array_build(self::all(), function ($k, $v) {
                return [$v['id'], $v];
            });
        });
    }

    /**
     * @return array
     *
     * @Util 获取所有栏目Tree
     */
    public static function tree()
    {
        $cats = CmsCatUtil::all();
        $catTree = TreeUtil::nodesToTree($cats);
        return $catTree;
    }


    private static function treeMergeMemberCanPost(&$tree, \Closure $callbackCanPost)
    {
        foreach ($tree as $i => $v) {
            $childMemberCanPostCount = 0;
            if (!empty($v['_child'])) {
                self::treeMergeMemberCanPost($tree[$i]['_child'], $callbackCanPost);
                foreach ($tree[$i]['_child'] as $item) {
                    if ($item['_memberCanPost']) {
                        $childMemberCanPostCount++;
                    }
                }
            }
            $tree[$i]['_memberCanPost'] = call_user_func_array($callbackCanPost, [$v]);
            $tree[$i]['_childMemberCanPostCount'] = $childMemberCanPostCount;
        }
    }

    private static function filterMemberCanPost($tree)
    {
        $newTree = [];
        foreach ($tree as $item) {
            if (!$item['_memberCanPost'] && $item['_childMemberCanPostCount'] <= 0) {
                continue;
            }
            if (!empty($item['_child'])) {
                $item['_child'] = self::filterMemberCanPost($item['_child']);
            }
            $newTree[] = $item;
        }
        return $newTree;
    }

    public static function treeWithPost(\Closure $callbackCanPost)
    {
        $catTree = CmsCatUtil::tree();
        self::treeMergeMemberCanPost($catTree, $callbackCanPost);
        return self::filterMemberCanPost($catTree);
    }

    /**
     * @param $catId int 分类ID
     * @param $includeSelf bool 是否包括自己
     * @return array
     *
     * @Util 获取所有子栏目ID
     */
    public static function childrenIds($catId, $includeSelf = true)
    {
        $nodes = self::all();
        $catIds = TreeUtil::nodesChildrenIds($nodes, $catId);
        if ($includeSelf) {
            return array_merge($catIds, [$catId]);
        }
        return $catIds;
    }

    public static function children($catId)
    {
        $children = [];
        foreach (self::all() as $cat) {
            if ($cat['pid'] == $catId) {
                $children[] = $cat;
            }
        }
        return ArrayUtil::sortByKey($children, 'sort', 'asc');
    }

    public static function root($catId)
    {
        $id = $catId;
        for ($i = 0; $i < 10; $i++) {
            $cat = self::get($id);
            if (!$cat['pid']) {
                return $cat;
            }
            $id = $cat['pid'];
        }
        return null;
    }

    public static function chain($id)
    {
        $nodes = self::all();
        return TreeUtil::nodesChain($nodes, $id);
    }

    public static function getByUrl($url)
    {
        foreach (self::all() as $item) {
            if ($item['url'] == $url) {
                return $item;
            }
        }
        return null;
    }

    /**
     * @param $id int
     * @return mixed|null
     *
     * @Util 获取一个子栏目
     */
    public static function get($id)
    {
        $map = self::map();
        return isset($map[$id]) ? $map[$id] : null;
    }

    public static function allSafely()
    {
        try {
            return self::all();
        } catch (\Exception $e) {
        }
        return [];
    }

    public static function allSafelyHavingUrl()
    {
        return array_filter(self::allSafely(), function ($item) {
            return !empty($item['url']) && !empty($item['_model']['mode']);
        });
    }

    public static function build($modelName, $cat, $catParentUrl = null)
    {
        $model = ModelUtil::get('cms_model', ['name' => $modelName]);
        if (!isset($cat['pid'])) {
            $cat['pid'] = 0;
        }
        if (!isset($cat['sort'])) {
            $cat['sort'] = 0;
        }
        $cat['modelId'] = $model['id'];
        if ($catParentUrl) {
            $parentCat = ModelUtil::get('cms_cat', ['url' => $catParentUrl]);
            $cat['pid'] = $parentCat['id'];
        }
        ModelUtil::insert('cms_cat', $cat);
    }

}
