<?php


namespace Module\Member\Util;

use ModStart\Core\Dao\ModelUtil;
use Module\Vendor\Cache\CacheUtil;

class MemberGroupUtil
{
    public static function all()
    {
        return CacheUtil::rememberForever('MemberGroupList', function () {
            return ModelUtil::all('member_group', [], ['*'], ['id', 'asc']);
        });
    }

    public static function mapIdTitle()
    {
        return array_build(self::all(), function ($k, $v) {
            return [$v['id'], $v['title']];
        });
    }


    public static function map()
    {
        return CacheUtil::rememberForever('MemberGroupMap', function () {
            return array_build(self::all(), function ($k, $v) {
                return [$v['id'], $v];
            });
        });
    }

    public static function get($groupId)
    {
        static $map = null;
        if (null === $map) {
            $map = self::map();
        }
        if (empty($groupId)) {
            foreach ($map as $item) {
                if ($item['isDefault']) {
                    return $item;
                }
            }
            return null;
        }
        if (isset($map[$groupId])) {
            return $map[$groupId];
        }
        return null;
    }


    public static function clearCache()
    {
        CacheUtil::forget('MemberGroupList');
        CacheUtil::forget('MemberGroupMap');
    }
}
