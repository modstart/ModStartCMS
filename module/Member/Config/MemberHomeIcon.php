<?php


namespace Module\Member\Config;


use Illuminate\Support\Str;

/**
 * 用户首页图标
 */
class MemberHomeIcon
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
        $menu = self::mergeMenu($menu);
        return $menu;
    }

    private static function sort()
    {
        static $sort = 1000;
        return $sort++;
    }


    private static function mergeMenu($menu, $prefix = '', $level = 1, $filter = null)
    {
        if (empty($menu)) {
            return [];
        }
        $flatten = [];
        foreach ($menu as $item) {
            if (empty($item)) {
                continue;
            }
            if ($filter) {
                if (!call_user_func($filter, $item)) {
                    continue;
                }
            }
            if (!isset($item['sort'])) {
                $item['sort'] = self::sort();
            }
            if (1 == $level) {
                $k = $prefix . $item['title'] . '^' . $item['sort'];
            } else {
                $k = $prefix . $item['title'];
            }
            if (!isset($flatten[$k])) $flatten[$k] = $item;
            unset($flatten[$k]['children']);
            if (!empty($item['children'])) {
                $flatten = array_merge($flatten, self::mergeMenu($item['children'], $k . '^^', $level + 1, $filter));
            }
        }
        if ($level == 1) {
            uasort($flatten, function ($a, $b) {
                return $a['sort'] - $b['sort'];
            });
            return self::mergeMenuTree($flatten);
        }
        return $flatten;
    }


    private static function mergeMenuTree($flaten, $prefix = '')
    {
        $tree = [];
        foreach ($flaten as $k => $v) {
            if ($k === $prefix) {
                continue;
            }
            $ks = explode('^^', $k);
            if ($prefix) {
                if (!Str::startsWith($k, $prefix)) {
                    continue;
                }
                if (count(explode('^^', $prefix)) !== count(explode('^^', $k))) {
                    continue;
                }
                // echo "$prefix -> $k\n";
            } else {
                if (1 != count($ks)) {
                    continue;
                }
            }
            $item = $v;
            $item['children'] = self::mergeMenuTree($flaten, $k . '^^');
            if (empty($item['children'])) {
                unset($item['children']);
            }
            if (empty($item['children']) && empty($item['url'])) continue;
            $tree[] = $item;
        }
        // echo $prefix . ' -> ' . json_encode($tree) . "\n";
        return $tree;
    }
}
