<?php


namespace Module\Member\Util;


use ModStart\Core\Exception\BizException;
use Module\Member\Auth\MemberUser;
use Module\Vendor\Util\AtomicUtil;

class MemberAtomicUtil
{
    public static function acquireOrFail($msg, $prefix, $memberUserId = null, $expire = 30)
    {
        if (!self::acquire($prefix, $memberUserId, $expire)) {
            BizException::throws($msg);
        }
    }

    public static function acquire($prefix, $memberUserId = null, $expire = 30)
    {
        if (null === $memberUserId) {
            $memberUserId = MemberUser::id();
        }
        $key = $prefix . '_' . $memberUserId;
        return AtomicUtil::acquire($key, $expire);
    }

    public static function release($prefix, $memberUserId = null)
    {
        if (null === $memberUserId) {
            $memberUserId = MemberUser::id();
        }
        $key = $prefix . '_' . $memberUserId;
        AtomicUtil::release($key);
    }
}
