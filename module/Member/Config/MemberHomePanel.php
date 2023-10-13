<?php


namespace Module\Member\Config;


/**
 * 用户首页图标
 */
class MemberHomePanel
{
    private static $list = [];

    public static function register($item)
    {
        self::$list[] = $item;
    }

    public static function get()
    {
        foreach (self::$list as $k => $item) {
            if ($item instanceof \Closure) {
                self::$list[$k] = call_user_func($item);
            }
        }
        return self::$list;
    }
}
