<?php


namespace Module\Member\Config;


use Illuminate\Support\Str;
use ModStart\Core\Util\RenderUtil;

/**
 * 右上角菜单
 */
class MemberNavMenu
{
    private static $menu = [];

    public static function register($menu)
    {
        self::$menu[] = $menu;
    }

    public static function get()
    {
        $menu = [];
        foreach (self::$menu as $item) {
            if ($item instanceof \Closure) {
                $item = call_user_func($item);
            }
            $menu = array_merge($menu, $item);
        }
        return $menu;
    }

    public static function render()
    {
        $items = self::get();
        if (empty($items)) {
            return '';
        }
        return RenderUtil::view('module::Member.View.inc.memberNavMenu', [
            'items' => $items,
        ]);
    }

}
