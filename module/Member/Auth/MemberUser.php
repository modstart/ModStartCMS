<?php


namespace Module\Member\Auth;


use Illuminate\Support\Facades\Session;
use Module\Member\Util\MemberGroupUtil;
use Module\Member\Util\MemberUtil;
use Module\Member\Util\MemberVipUtil;

class MemberUser
{
    public static function id()
    {
        return intval(Session::get('memberUserId', 0));
    }

    public static function user()
    {
        return Session::get('_memberUser', null);
    }

    public static function update($memberUser)
    {
        if (!empty($memberUser)) {
            Session::put('_memberUser', $memberUser);
            Session::put('memberUserId', $memberUser['id']);
        } else {
            Session::forget('_memberUser');
            Session::forget('memberUserId');
        }
    }

    public static function isMine($memberUserId)
    {
        return self::id() > 0 && self::id() == $memberUserId;
    }

    public static function isNotMine($memberUserId)
    {
        return !self::isMine($memberUserId);
    }

    public static function isLogin()
    {
        return self::id() > 0;
    }

    public static function isNotLogin()
    {
        return !self::isLogin();
    }

    public static function isGroup($groupIds)
    {
        if (self::isNotLogin()) {
            return false;
        }
        if (!is_array($groupIds)) {
            $groupIds = [intval($groupIds)];
        }
        $groupId = intval(self::get('groupId', 0));
        if ($groupId == 0) {
            $groupId = MemberGroupUtil::defaultGroupId();
        }
        return in_array($groupId, $groupIds);
    }

    public static function isVip($vipIds)
    {
        if (self::isNotLogin()) {
            return false;
        }
        if (!is_array($vipIds)) {
            $vipIds = [intval($vipIds)];
        }
        $vip = MemberVipUtil::getMemberVip(self::user());
        $vipId = 0;
        if (!empty($vip)) {
            $vipId = $vip['id'];
        }
        return in_array($vipId, $vipIds);
    }

    public static function get($key = null, $default = null)
    {
        $user = self::user();
        if (null === $key) {
            return $user;
        }
        return isset($user[$key]) ? $user[$key] : $default;
    }

    /**
     * @return mixed|string
     * @deprecated delete at 2024-07-09
     */
    public static function nickname()
    {
        return MemberUtil::viewName(self::user());
    }

    public static function viewName()
    {
        return MemberUtil::viewName(self::user());
    }
}
