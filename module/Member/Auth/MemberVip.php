<?php


namespace Module\Member\Auth;


use Module\Member\Util\MemberVipUtil;

class MemberVip
{
    public static function get($key = null)
    {
        static $memberVip = null;
        if (null === $memberVip) {
            $memberVip = MemberVipUtil::getMemberVip(MemberUser::user());
        }
        if (null !== $key) {
            return $memberVip[$key];
        }
        return $memberVip;
    }
}
