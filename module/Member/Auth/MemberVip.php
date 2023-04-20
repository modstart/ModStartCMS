<?php


namespace Module\Member\Auth;


use Module\Member\Util\MemberVipUtil;

class MemberVip
{
    public static function get($key = null, $defaultValue = null)
    {
        static $memberVip = null;
        if (null === $memberVip) {
            $memberVip = MemberVipUtil::getMemberVip(MemberUser::user());
        }
        if (null !== $key) {
            return isset($memberVip[$key]) ? $memberVip[$key] : $defaultValue;
        }
        return $memberVip;
    }

    public static function isDefault()
    {
        return self::get('isDefault', false);
    }

    public static function id()
    {
        return self::get('id', 0);
    }

    public static function is($vipIds)
    {
        if (empty($vipIds)) {
            return false;
        }
        if (!is_array($vipIds)) {
            $vipIds = [$vipIds];
        }
        return in_array(self::id(), $vipIds);
    }
}
