<?php


namespace ModStart\Admin\Auth;


use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use ModStart\Admin\Config\AdminConfig;
use ModStart\Admin\Config\AdminMenu;
use ModStart\Core\Input\Request;
use ModStart\Core\Input\Response;

class AdminPermission
{
    /**
     * @deprecated
     */
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
        return intval(AdminConfig::get('founderId', 1));
    }

    public static function isUrlAction($url)
    {
        if (empty($url)) {
            return false;
        }
        return preg_match('/^[\w\\\\]+@[\w]+$/', $url);
    }

    public static function urlToLink($url)
    {
        if (empty($url)) {
            return 'javascript:;';
        }
        if (self::isUrlAction($url)) {
            return action($url);
        }
        return $url;
    }

    private static function mergeMenuTree($flatten, $prefix = '', $ruleMode = false)
    {
        $tree = [];
        foreach ($flatten as $k => $v) {
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
            $item['children'] = self::mergeMenuTree($flatten, $k . '^^', $ruleMode);
            if (empty($item['children'])) {
                unset($item['children']);
            }
            if (!$ruleMode) {
                if (empty($item['children']) && empty($item['url'])) continue;
            } else {
                if (empty($item['children']) && empty($item['rule'])) continue;
            }
            $tree[] = $item;
        }
        // echo $prefix . ' -> ' . json_encode($tree) . "\n";
        return $tree;
    }

    private static function sort()
    {
        static $sort = 10000;
        return $sort++;
    }

    private static function mergeMenu($menu, $prefix = '', $level = 1, $filter = null, $ruleMode = false)
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
                    // build icon
                    $item['icon'] = "<i class='icon iconfont icon-$item[icon]'></i>";
                } else if (preg_match('/^[a-zA-Z0-9\\- ]+$/', $item['icon'])) {
                    // custom icon class
                    $item['icon'] = "<i class='icon $item[icon]'></i>";
                } else {
                    // custom icon dom
                }
            }
            if (!isset($flatten[$k])) $flatten[$k] = $item;
            unset($flatten[$k]['children']);
            if (!empty($item['children'])) {
                $flatten = array_merge($flatten, self::mergeMenu($item['children'], $k . '^^', $level + 1, $filter, $ruleMode));
            }
        }
        if ($level == 1) {
            uasort($flatten, function ($a, $b) {
                return $a['sort'] - $b['sort'];
            });
            return self::mergeMenuTree($flatten, '', $ruleMode);
        }
        return $flatten;
    }

    private static function menuClean($menu, $menuFlat)
    {
        $newMenu = [];
        foreach ($menu as $item) {
            if (!empty($item['url'])) {
                if (in_array($item['url'], $menuFlat)) {
                    continue;
                }
            }
            if (!empty($item['children'])) {
                $item['children'] = self::menuClean($item['children'], $menuFlat);
            }
            $newMenu[] = $item;
        }
        return $newMenu;
    }

    private static function flatMenuUrl($menu)
    {
        $urls = [];
        foreach ($menu as $item) {
            if (!empty($item['url'])) {
                $urls[] = $item['url'];
            }
            if (!empty($item['children'])) {
                $urls = array_merge($urls, self::flatMenuUrl($item['children']));
            }
        }
        return $urls;
    }

    public static function menuAll(\Closure $filter = null, $ruleMode = false)
    {
        $menu = AdminConfig::get('menu', []);
        $moduleMenu = AdminMenu::get();
        // print_r($moduleMenu);exit();
        $menuAll = array_merge($menu, $moduleMenu);
        $menuCustom = modstart_config()->getArray('adminMenuCustom', []);
        $menuCustomFlat = self::flatMenuUrl($menuCustom);
        $menuAfterCustom = self::menuClean($menuAll, $menuCustomFlat);
        $menuAll = array_merge($menuCustom, $menuAfterCustom);
        // print_r($menuAll);exit();
        $menu = self::mergeMenu($menuAll, '', 1, $filter, $ruleMode);
        // print_r($menu);exit();
        return $menu;
    }

    public static function menu($controllerMethod, $menu = null)
    {
        static $currentUrl = null;
        if (null === $currentUrl) {
            $currentUrl = Request::currentPageUrl();
        }
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
                // echo "$controllerMethod - $v[url] - $currentUrl\n";
                if ($controllerMethod === $v['url']
                    || $currentUrl === $v['url']
                    || ends_with($currentUrl, $v['url'])
                    || (!empty($v['active']) && in_array($controllerMethod, $v['active']))
                ) {
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
        /*
        if ($adminUser && $adminUser['id'] == AdminPermission::founderId()) {
            return true;
        }
        */
        if (!isset($adminRules[$rule])) {
            return false;
        }
        // print_r($adminRules);exit();
        // echo "$rule -> false\n";
        return $adminRules[$rule]['auth'] ? true : false;
    }

    public static function rules($menu = null)
    {
        if (null === $menu) {
            $menu = AdminConfig::get('menu', []);
            $moduleMenu = AdminMenu::get();
            $menu = self::mergeMenu(array_merge($menu, $moduleMenu), '', 1, null, true);
        }
        if (empty($menu)) {
            return [];
        }
        $rules = [];
        foreach ($menu as $menuItem) {
            if (!empty($menuItem['rule'])) {
                $rules[$menuItem['rule']] = [
                    'url' => isset($menuItem['url']) ? $menuItem['url'] : '',
                    'rule' => $menuItem['rule'],
                ];
            }
            if (!empty($menuItem['children'])) {
                $rules = array_merge($rules, self::rules($menuItem['children']));
            }
        }
        return $rules;
    }
}
