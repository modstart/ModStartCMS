<?php


namespace Module\Member\Auth;


use Module\Member\Util\MemberGroupUtil;
use Module\Member\Util\MemberVipUtil;

class MemberGroup
{
    public static function get($key = null)
    {
        static $memberGroup = null;
        if (null === $memberGroup) {
            $memberGroup = MemberGroupUtil::getMemberGroup(MemberUser::user());
        }
        if (null !== $key) {
            return $memberGroup[$key];
        }
        return $memberGroup;
    }
}