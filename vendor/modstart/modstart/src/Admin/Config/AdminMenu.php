<?php


namespace ModStart\Admin\Config;


use Illuminate\Support\Facades\Log;
use ModStart\Core\Util\SerializeUtil;

class AdminMenu
{
    private static $menu = [];

    public static function register($menu)
    {
        if (is_array($menu)) {
            Log::warning('AdminMenu.register is not callable - ' . SerializeUtil::jsonEncode($menu));
        }
        self::$menu[] = $menu;
    }

    public static function get()
    {
        $menu = [];
        foreach (self::$menu as $item) {
            if ($item instanceof \Closure) {
                $item = call_user_func($item);
            }
            if (isset($item['title'])) {
                $menu = array_merge($menu, [$item]);
            } else {
                $menu = array_merge($menu, $item);
            }
        }
        return $menu;
    }
}
