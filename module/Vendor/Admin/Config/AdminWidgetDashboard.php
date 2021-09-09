<?php


namespace Module\Vendor\Admin\Config;

use ModStart\Admin\Layout\AdminPage;
use ModStart\Layout\Row;

class AdminWidgetDashboard
{
    private static $icon = [];
    private static $foot = [];

    public static function registerIcon($closure)
    {
        self::$icon[] = $closure;
    }

    public static function callIcon(Row $row)
    {
        foreach (self::$icon as $item) {
            call_user_func_array($item, [$row]);
        }
    }

    public static function registerFoot($closure)
    {
        self::$foot[] = $closure;
    }

    public static function call(AdminPage $page)
    {
        foreach (self::$foot as $item) {
            if ($item instanceof \Closure) {
                call_user_func_array($item, [$page]);
            }
        }
    }
}
