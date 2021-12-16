<?php


namespace Module\Cms\Util;


use Illuminate\Support\Facades\Cache;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Util\TreeUtil;

/**
 * Class CmsCatUtil
 * @package Module\Cms\Util
 *
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
    }

    /**
     * @return mixed
     *
     * @Util 获取所有栏目
     */
    public static function all()
    {
        return Cache::rememberForever('CmsCatAll', function () {
            $records = ModelUtil::all('cms_cat');
            ModelUtil::decodeRecordsJson($records, ['visitMemberGroups', 'visitMemberVips']);
            return $records;
        });
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
        return $children;
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
        foreach (self::all() as $item) {
            if ($item['id'] == $id) {
                return $item;
            }
        }
        return null;
    }

    public static function allSafely()
    {
        try {
            return self::all();
        } catch (\Exception $e) {
        }
        return [];
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
