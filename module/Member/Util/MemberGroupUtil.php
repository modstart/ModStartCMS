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


    public static function clearCache()
    {
        CacheUtil::forget('MemberGroupList');
        CacheUtil::forget('MemberGroupMap');
    }
}
