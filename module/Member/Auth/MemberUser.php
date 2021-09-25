<?php


namespace Module\Member\Auth;


use Illuminate\Support\Facades\Session;

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

    public static function get($key = null, $default = null)
    {
        $user = self::user();
        if (null === $key) {
            return $user;
        }
        return isset($user[$key]) ? $user[$key] : $default;
    }
}
