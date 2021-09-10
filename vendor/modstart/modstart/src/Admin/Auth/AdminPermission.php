<?php


namespace ModStart\Admin\Auth;


use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use ModStart\Admin\Config\AdminConfig;
use ModStart\Admin\Config\AdminMenu;
use ModStart\Core\Input\Request;
use ModStart\Core\Input\Response;
use ModStart\Module\ModuleManager;

class AdminPermission
{
    public static function demoPostCheck()
    {
        if (self::isDemo() && Request::isPost()) {
            Response::quit(-1, L('Operate Forbidden For Demo Account'));
        }
    }

    public static function demoCheck()
    {
        if (self::isDemo()) {
            Response::quit(-1, L('Operate Forbidden For Demo Account'));
        }
    }

    public static function demoResponse()
    {
        return Response::send(-1, L('Operate Forbidden For Demo Account'));
    }

    public static function permitCheck($rule)
    {
        if (!self::permit($rule)) {
            Response::quit(-1, L('No Permission'));
        }
    }

    public static function isDemo()
    {
        return Admin::id() === self::demoId();
    }

    public static function demoId()
    {
        return intval(AdminConfig::get('demoId', 0));
    }

    public static function isFounder($adminUserId)
    {
        return self::founderId() === $adminUserId;
    }

    public static function founderId()
    {
        return AdminConfig::get('founderId', 1);
    }

    public static function urlToLink($url)
    {
        if (empty($url)) {
            return 'javascript:;';
        }
        if (preg_match('/^[\w\\\\]+@[\w]+$/', $url)) {
            return action($url);
        }
        return $url;
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
                return $tree;
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
                $k = $prefix . $item['title'] . '^' . $item['icon'] . '^' . $item['sort'];
            } else {
                $k = $prefix . $item['title'];
            }
            if (!isset($item['rule'])) {
                if (isset($item['url'])) {
                    $item['rule'] = $item['url'];
                } else {
                    $item['rule'] = '';
                }
            }
            if (!isset($item['hide'])) {
                $item['hide'] = false;
            }
            if (isset($item['icon'])) {
                if (preg_match('/^[a-zA-Z0-9\\-]+$/', $item['icon'])) {
                    $item['icon'] = "<i class='icon iconfont icon-$item[icon]'></i>";
                }
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

    public static function menuAll(\Closure $filter = null)
    {
        $menu = AdminConfig::get('menu', []);
        $moduleMenu = AdminMenu::get();
                $menuAll = array_merge($menu, $moduleMenu);
                $menu = self::mergeMenu($menuAll, '', 1, $filter);
                return $menu;
    }

    public static function menu($controllerMethod, $menu = null)
    {
        if (null === $menu) {
            $menu = self::menuAll();
        }
        if (empty($menu)) {
            return [];
        }
        $menuNew = [];
        foreach ($menu as $k => $v) {
            if (!empty($v['hide'])) {
                continue;
            }
            if (empty($v['children'])) {
                if ($controllerMethod === $v['url']) {
                    $v['_active'] = true;
                }
                if (self::permit($v['rule'])) {
                    $menuNew[] = $v;
                }
            } else {
                $v['children'] = self::menu($controllerMethod, $v['children']);
                foreach ($v['children'] as $child) {
                    if (!empty($child['_active'])) {
                        $v['_active'] = true;
                        break;
                    }
                }
                if (!empty($v['children']) || ($v['rule'] && self::permit($v['rule']))) {
                    $menuNew[] = $v;
                }
            }
        }
        return $menuNew;
    }

    public static function isNotPermit($rule)
    {
        return !self::permit($rule);
    }

    public static function permit($rule)
    {
        static $adminRules = null;
        static $adminUser = null;
        if (null === $adminRules) {
            $adminRules = Session::get('_adminRules');
            $adminUser = Session::get('_adminUser');
        }
        if ($adminUser && $adminUser['id'] == AdminPermission::founderId()) {
            return true;
        }
        if (!isset($adminRules[$rule])) {
            return true;
        }
                        return $adminRules[$rule] ? true : false;
    }

    public static function rules($menu = null)
    {
        if (null === $menu) {
            $menu = AdminConfig::get('menu', []);
            $moduleMenu = AdminMenu::get();
            $menu = self::mergeMenu(array_merge($menu, $moduleMenu));
        }
        if (empty($menu)) {
            return [];
        }
        $rules = [];
        foreach ($menu as $menuItem) {
            if (!empty($menuItem['rule'])) {
                $rules[] = $menuItem['rule'];
            }
            if (!empty($menuItem['children'])) {
                $rules = array_merge($rules, self::rules($menuItem['children']));
            }
        }
        return array_unique($rules);
    }
}
