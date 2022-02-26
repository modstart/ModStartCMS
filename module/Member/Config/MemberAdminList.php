<?php


namespace Module\Member\Config;


class MemberAdminList
{
    private static $gridFields = [];

    public static function registerGridField(\Closure $callback)
    {
        self::$gridFields[] = $callback;
    }

    public static function callGridField($builder)
    {
        foreach (self::$gridFields as $callback) {
            call_user_func_array($callback, [$builder]);
        }
    }
}
