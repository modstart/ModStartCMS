<?php


namespace Module\Vendor\Admin\Config;

use ModStart\Admin\Auth\AdminPermission;
use ModStart\Admin\Layout\AdminPage;
use ModStart\Layout\Row;
use ModStart\Widget\Box;
use Module\Vendor\Provider\ContentVerify\ContentVerifyBiz;

/**
 * Class AdminWidgetDashboard
 * @package Module\Vendor\Admin\Widget
 * @deprecated delete 2023-10-13
 */
class AdminWidgetDashboard
{
    private static $icon = [];
    private static $foot = [];

    /**
     * @param Row $row
     * @deprecated delete after 2023-09-01
     */
    public static function registerTodo($closure)
    {
    }

    /**
     * @param Row $row
     * @deprecated delete after 2023-09-01
     */
    public static function callTodo(Row $row)
    {
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
        foreach (ContentVerifyBiz::listAll() as $provider) {
            if (AdminPermission::permit($provider->verifyRule())) {
                $cnt = $provider->verifyCount();
                if ($cnt > 0) {
                    $url = $provider->verifyUrl();
                    $title = $provider->title();
                    $verifyHtml[] = "<a class='tw-mr-2 tw-mb-2 tw-inline-block tw-bg-yellow-100 tw-text-yellow-600 tw-py-1 tw-px-2 tw-rounded-2xl'
                                     href='$url' data-tab-open data-tab-title='$title'>$title <span class='ub-text-danger tw-font-bold'>$cnt</span> 条</a>";
                }
            }
        }
        if (!empty($verifyHtml)) {
            $page->row(Box::make(join("", $verifyHtml), '<i class="iconfont icon-details"></i> 待审核'));
        }

        foreach (self::$foot as $item) {
            if ($item instanceof \Closure) {
                call_user_func_array($item, [$page]);
            }
        }
    }
}
