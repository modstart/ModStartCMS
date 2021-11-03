<?php


namespace Module\Member\Util;

use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Input\Response;
use Module\Vendor\Cache\CacheUtil;

class MemberGroupUtil
{

    public static function clearCache()
    {
        CacheUtil::forget('MemberGroupList');
        CacheUtil::forget('MemberGroupMap');
    }
}
