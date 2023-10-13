<?php


namespace Module\Member\Config;


/**
 * 用户管理后台配置
 */
class MemberAdminList
{
    private static $gridFields = [];

    /**
     * @param \Closure $callback function($builder) {}
     */
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
