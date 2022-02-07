<?php


namespace Module\Vendor\Admin\Config;

use ModStart\Admin\Auth\AdminPermission;
use ModStart\Admin\Layout\AdminPage;
use ModStart\Layout\Row;
use ModStart\Widget\Box;
use Module\Vendor\Provider\ContentVerify\ContentVerifyProvider;

class AdminWidgetDashboard
{
    private static $todo = [];
    private static $icon = [];
    private static $foot = [];

    public static function registerTodo($closure)
    {
        self::$todo[] = $closure;
    }

    public static function callTodo(Row $row)
    {
        foreach (self::$todo as $item) {
            call_user_func_array($item, [$row]);
        }
    }

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
        $verifyHtml = [];
        foreach (ContentVerifyProvider::all() as $provider) {
            if (AdminPermission::permit($provider->verifyRule())) {
                $cnt = $provider->verifyCount();
                if ($cnt > 0) {
                    $url = $provider->verifyUrl();
                    $title = $provider->title();
                    $verifyHtml[] = "<a class='tw-mr-4 tw-inline-block' href='$url'>$title<span class='ub-text-danger'>$cnt</span>条</a>";
                }
            }
        }
        if (!empty($verifyHtml)) {
            $page->row(Box::make(join("", $verifyHtml), '待审核'));
        }

        foreach (self::$foot as $item) {
            if ($item instanceof \Closure) {
                call_user_func_array($item, [$page]);
            }
        }
    }
}
