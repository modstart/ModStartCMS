<?php


namespace ModStart\Core\Util;

use Illuminate\Support\Facades\Cache;
use ModStart\Core\Dao\ModelUtil;

class CmsUtil
{
    private static function _CmsCacheRegister($tag, $key)
    {
        $tagKey = "CmsKeys:$tag";
        if (DBLockUtil::acquire('CmsCache.' . $tagKey)) {
            $tagValues = Cache::get($tagKey);
            if (!is_array($tagValues)) {
                $tagValues = [];
            }
            $tagValues[$key] = true;
            Cache::forever($tagKey, $tagValues);
            DBLockUtil::release('CmsCache.' . $tagKey);
        }
    }

    private static function _CmsCacheFlush($tag)
    {
        $tagKey = "CmsKeys:$tag";
        $tagValues = Cache::get($tagKey);
        if (!is_array($tagValues)) {
            return;
        }
        foreach ($tagValues as $k => $_) {
            Cache::forget($k);
        }
    }

    private static function CmsCacheClear($tag)
    {
        self::_CmsCacheFlush($tag);
    }


    public static function CmsCategoryAll($table, $where = [], $fields = ['*'], $order = ['sort', 'asc'], $cacheSeconds = 3600)
    {
        $key = "CmsCategory:$table:All:" . md5(serialize([$where, $fields, $order]));
        return Cache::remember(
            $key,
            $cacheSeconds,
            function () use ($table, $where, $fields, $order, $key) {
                self::_CmsCacheRegister($table, $key);
                $all = ModelUtil::all($table, $where, $fields, $order);
                return $all;
            }
        );
    }

    public static function CmsCategoryTree($table, $fieldsMap = ['name'], $keyId = 'id', $keyPid = 'pid', $keySort = 'sort', $cacheSeconds = 3600)
    {
        $key = "CmsCategory:$table:Tree:" . md5(serialize([$fieldsMap, $keyId, $keyPid, $keySort]));
        return Cache::remember(
            $key,
            $cacheSeconds,
            function () use ($table, $fieldsMap, $keyId, $keyPid, $keySort, $key) {
                self::_CmsCacheRegister($table, $key);
                $tree = \TechOnline\Laravel\Util\TreeUtil::model2Nodes($table, $fieldsMap, $keyId, $keyPid, $keySort);
                return $tree;
            }
        );
    }

    public static function CmsCategoryChildrenIds($table, $id, $fieldsMap = ['name'], $keyId = 'id', $keyPid = 'pid', $keySort = 'sort', $cacheSeconds = 3600)
    {
        $key = "CmsCategory:$table:CIds:" . md5(serialize([$id, $fieldsMap, $keyId, $keyPid, $keySort]));
        return Cache::remember(
            $key,
            $cacheSeconds,
            function () use ($table, $id, $fieldsMap, $keyId, $keyPid, $keySort, $key) {
                self::_CmsCacheRegister($table, $key);
                $all = ModelUtil::all($table, [], ['*'], [$keySort, 'asc']);
                $ids = \TechOnline\Laravel\Util\TreeUtil::allChildIds($all, $id, $keyId, $keyPid);
                return $ids;
            }
        );
    }

    function CmsCategoryGet($table, $where = [], $fields = ['*'], $cacheSeconds = 3600)
    {
        $key = "CmsCategory:$table:Get:" . md5(serialize([$where, $fields]));
        return Cache::remember(
            $key,
            $cacheSeconds,
            function () use ($table, $where, $fields, $key) {
                self::_CmsCacheRegister($table, $key);
                $one = ModelUtil::get($table, $where, $fields);
                return $one;
            }
        );
    }


    function CmsBasicAll($table, $where = [], $fields = ['*'], $order = null, $cacheSeconds = 3600)
    {
        $key = "CmsBasic:$table:All:" . md5(serialize([$where, $fields, $order]));
        return Cache::remember(
            $key,
            $cacheSeconds,
            function () use ($table, $where, $fields, $order, $key) {
                self::_CmsCacheRegister($table, $key);
                $all = ModelUtil::all($table, $where, $fields, $order);
                return $all;
            }
        );
    }

    function CmsBasicLimit($table, $where = [], $fields = ['*'], $order = null, $limit = 10, $cacheSeconds = 3600, $option = [])
    {
        $key = "CmsBasic:$table:Limit:" . md5(serialize([$where, $fields, $order, $limit, $option]));
        return Cache::remember(
            $key,
            $cacheSeconds,
            function () use ($table, $where, $fields, $order, $limit, $key, $option) {
                self::_CmsCacheRegister($table, $key);
                $m = ModelUtil::model($table);
                $m = $m->where($where)->limit($limit);
                if ($order !== null) {
                    $m = $m->orderBy($order[0], $order[1]);
                }
                if (!empty($option['whereIn'])) {
                    $m = $m->whereIn($option['whereIn'][0], $option['whereIn'][1]);
                }
                $limits = $m->get($fields)->toArray();
                return $limits;
            }
        );
    }

    function CmsBasicGet($table, $where = [], $fields = ['*'], $cacheSeconds = 3600)
    {
        $key = "CmsBasic:$table:Get:" . md5(serialize([$where, $fields]));
        return Cache::remember(
            $key,
            $cacheSeconds,
            function () use ($table, $where, $fields, $key) {
                self::_CmsCacheRegister($table, $key);
                $one = ModelUtil::get($table, $where, $fields);
                return $one;
            }
        );
    }

    function CmsBasicGetField($table, $field, $where = [], $fields = ['*'], $cacheSeconds = 3600)
    {
        $one = CmsBasicGet($table, $where, $fields, $cacheSeconds);
        if (empty($one[$field])) {
            return null;
        }
        return $one[$field];
    }
}
