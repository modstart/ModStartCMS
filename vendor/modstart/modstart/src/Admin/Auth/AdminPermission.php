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

    //private static function mergeMenuTree($flatten, $prefix = '', $ruleMode = false)
    //{
    //    $tree = [];
    //    foreach ($flatten as $k => $v) {
    //        if ($k === $prefix) {
    //            continue;
    //        }
    //        $ks = explode('^^', $k);
    //        if ($prefix) {
    //            if (!Str::startsWith($k, $prefix)) {
    //                continue;
    //            }
    //            if (count(explode('^^', $prefix)) !== count(explode('^^', $k))) {
    //                continue;
    //            }
    //            // echo "$prefix -> $k\n";
    //        } else {
    //            if (1 != count($ks)) {
    //                continue;
    //            }
    //        }
    //        $item = $v;
    //        $item['children'] = self::mergeMenuTree($flatten, $k . '^^', $ruleMode);
    //        if (empty($item['children'])) {
    //            unset($item['children']);
    //        }
    //        if (!$ruleMode) {
    //            if (empty($item['children']) && empty($item['url'])) continue;
    //        } else {
    //            if (empty($item['children']) && empty($item['rule'])) continue;
    //        }
    //        $tree[] = $item;
    //    }
    //    // echo $prefix . ' -> ' . json_encode($tree) . "\n";
    //    return $tree;
    //}

    private static function sort()
    {
        static $sort = 10000;
        return $sort++;
    }

    private static function menuMergeCustomMarkCustom($menu, $urls)
    {
        $urlInfoMap = [];
        foreach ($menu as $i => $item) {
            $menu[$i]['_custom'] = false;
            if ($item['url']) {
                if (in_array($item['url'], $urls)) {
                    $menu[$i]['_custom'] = true;
                    $urlInfoMap[$item['url']] = $item;
                }
            }
            if (!empty($item['children'])) {
                list($m, $u) = self::menuMergeCustomMarkCustom($item['children'], $urls);
                $menu[$i]['children'] = $m;
                $urlInfoMap = array_merge($urlInfoMap, $u);
            }
        }
        return [$menu, $urlInfoMap];
    }

    private static function menuMergeCustomUpdateUrlInfo($menuCustom, $urlInfoMap)
    {
        foreach ($menuCustom as $i => $item) {
            if (isset($item['url']) && $item['url']) {
                if (isset($urlInfoMap[$item['url']])) {
                    $menuCustom[$i]['rule'] = $urlInfoMap[$item['url']]['rule'];
                    $menuCustom[$i]['hide'] = $urlInfoMap[$item['url']]['hide'];
                } else {
                    $menuCustom[$i]['_delete'] = true;
                }
            }
            if (!empty($item['children'])) {
                $menuCustom[$i]['children'] = self::menuMergeCustomUpdateUrlInfo($item['children'], $urlInfoMap);
            }
        }
        return $menuCustom;
    }

    private static function menuMergeCustomClean($menu)
    {
        $menuNew = [];
        foreach ($menu as $item) {
            if (isset($item['_delete']) && $item['_delete']) {
                continue;
            }
            unset($item['_delete']);
            if (!empty($item['children'])) {
                $item['children'] = self::menuMergeCustomClean($item['children']);
            }
            $menuNew[] = $item;
        }
        return $menuNew;
    }

    private static function menuMergeCustomOtherFlats($menu)
    {
        $menuNew = [];
        foreach ($menu as $i => $item) {
            if ($item['_custom']) {
                continue;
            }
            if (!empty($item['children'])) {
                $menuNew = array_merge($menuNew, self::menuMergeCustomOtherFlats($item['children']));
            }
            unset($item['children']);
            unset($item['_custom']);
            $menuNew[] = $item;
        }
        return $menuNew;
    }

    private static function menuMergeCustom($menu, $menuCustom)
    {
        $urls = self::menuFlatUrl($menuCustom);
        list($menu, $urlInfoMap) = self::menuMergeCustomMarkCustom($menu, $urls);
        $menuCustom = self::menuMergeCustomUpdateUrlInfo($menuCustom, $urlInfoMap);
        $menuCustom = self::menuMergeCustomClean($menuCustom);
        $menuOthers = self::menuMergeCustomOtherFlats($menu);
        if (!empty($menuOthers)) {
            $menuCustom = array_merge($menuCustom, [
                [
                    'icon' => '<i class="icon iconfont icon-cog"></i>',
                    'title' => 'Others',
                    'sort' => 99999,
                    'hide' => false,
                    'rule' => '',
                    'url' => '',
                    'children' => $menuOthers,
                ]
            ]);
        }
        return $menuCustom;
    }

    public static function menuMerge($menu)
    {
        $menu = self::normalMenu($menu);
        $menu = self::mergeMenuItem($menu);
        $menu = self::sortMenu($menu);
        $menu = self::menuRuleMode($menu, true);
        return $menu;
    }

    private static function sortMenu($menu)
    {
        foreach ($menu as $k => $item) {
            if (!empty($item['children'])) {
                $menu[$k]['children'] = self::sortMenu($item['children']);
            }
        }
        usort($menu, function ($a, $b) {
            return $a['sort'] - $b['sort'];
        });
        return $menu;
    }

    private static function normalMenu($menu, $level = 1)
    {
        $menuFilter = [];
        foreach ($menu as $k => $item) {
            if (empty($item)) {
                continue;
            }
            if (!isset($item['sort'])) {
                $item['sort'] = self::sort();
            }
            if (!isset($item['url'])) {
                $item['url'] = '';
            }
            if (!isset($item['icon'])) {
                $item['icon'] = '';
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
            if (!empty($item['children'])) {
                $item['children'] = self::normalMenu($item['children']);
            }
            if ($level === 1) {
                if (!isset($item['icon'])) {
                    $item['icon'] = 'list';
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
            }
            $menuFilter[] = $item;
        }
        return $menuFilter;
    }

    private static function mergeMenuItem($menu, $level = 1)
    {
        $map = [];
        foreach ($menu as $item) {
            if ($level === 1) {
                $title = $item['title'] . '^' . $item['icon'];
            } else {
                $title = $item['title'];
            }
            if (!isset($map[$title])) {
                $map[$title] = $item;
                $map[$title]['children'] = isset($item['children']) ? self::mergeMenuItem($item['children']) : [];
            } else {
                $newChildren = empty($item['children']) ? [] : $item['children'];
                $map[$title]['children'] = self::mergeMenuItem(array_merge($map[$title]['children'], $newChildren), $level + 1);
            }
        }
        return array_values($map);
    }

    private static function menuFilter($menu, \Closure $filter)
    {
        $menuNew = [];
        foreach ($menu as $item) {
            if (empty($item)) {
                continue;
            }
            if (!call_user_func($filter, $item)) {
                continue;
            }
            if (!isset($item['sort'])) {
                $item['sort'] = self::sort();
            }
            if (!isset($item['hide'])) {
                $item['hide'] = false;
            }
            if (!empty($item['children'])) {
                $item['children'] = self::menuFilter($item['children'], $filter);
            }
            $menuNew[] = $item;
        }
        return $menuNew;
    }

    private static function menuRuleMode($menu, $ruleMode)
    {
        $menuNew = [];
        foreach ($menu as $item) {
            if (!empty($item['children'])) {
                $item['children'] = self::menuRuleMode($item['children'], $ruleMode);
            }
            if (empty($item['children'])) {
                if ($ruleMode) {
                    if (!$item['rule']) {
                        continue;
                    }
                } else {
                    if (!$item['url']) {
                        continue;
                    }
                }
            }
            $menuNew[] = $item;
        }
        return $menuNew;
    }

    //private static function mergeMenu($menu, $prefix = '', $level = 1, $filter = null, $ruleMode = false)
    //{
    //    if (empty($menu)) {
    //        return [];
    //    }
    //    $flatten = [];
    //    foreach ($menu as $item) {
    //        if (empty($item)) {
    //            continue;
    //        }
    //        if ($filter) {
    //            if (!call_user_func($filter, $item)) {
    //                continue;
    //            }
    //        }
    //        if (!isset($item['sort'])) {
    //            $item['sort'] = self::sort();
    //        }
    //        if (1 == $level) {
    //            $k = $prefix . $item['title'] . '^' . $item['icon'] . '^' . $item['sort'];
    //        } else {
    //            $k = $prefix . $item['title'];
    //        }
    //        if (!isset($item['rule'])) {
    //            if (isset($item['url'])) {
    //                $item['rule'] = $item['url'];
    //            } else {
    //                $item['rule'] = '';
    //            }
    //        }
    //        if (!isset($item['hide'])) {
    //            $item['hide'] = false;
    //        }
    //        if (isset($item['icon'])) {
    //            if (preg_match('/^[a-zA-Z0-9\\-]+$/', $item['icon'])) {
    //                // build icon
    //                $item['icon'] = "<i class='icon iconfont icon-$item[icon]'></i>";
    //            } else if (preg_match('/^[a-zA-Z0-9\\- ]+$/', $item['icon'])) {
    //                // custom icon class
    //                $item['icon'] = "<i class='icon $item[icon]'></i>";
    //            } else {
    //                // custom icon dom
    //            }
    //        }
    //        if (!isset($flatten[$k])) $flatten[$k] = $item;
    //        unset($flatten[$k]['children']);
    //        if (!empty($item['children'])) {
    //            $flatten = array_merge($flatten, self::mergeMenu($item['children'], $k . '^^', $level + 1, $filter, $ruleMode));
    //        }
    //    }
    //    if ($level == 1) {
    //        uasort($flatten, function ($a, $b) {
    //            return $a['sort'] - $b['sort'];
    //        });
    //        return self::mergeMenuTree($flatten, '', $ruleMode);
    //    }
    //    return $flatten;
    //}

    //private static function menuClean($menu, $menuFlat)
    //{
    //    $newMenu = [];
    //    foreach ($menu as $item) {
    //        if (!empty($item['url'])) {
    //            if (in_array($item['url'], $menuFlat)) {
    //                continue;
    //            }
    //        }
    //        if (!empty($item['children'])) {
    //            $item['children'] = self::menuClean($item['children'], $menuFlat);
    //        }
    //        $newMenu[] = $item;
    //    }
    //    return $newMenu;
    //}

    private static function menuFlatUrl($menu)
    {
        $urls = [];
        foreach ($menu as $item) {
            if (!empty($item['url'])) {
                $urls[] = $item['url'];
            }
            if (!empty($item['children'])) {
                $urls = array_merge($urls, self::menuFlatUrl($item['children']));
            }
        }
        return $urls;
    }

    public static function menuAll(\Closure $filter = null, $ruleMode = false, $customOverwrite = true)
    {
        $menu = AdminConfig::get('menu', []);
        $moduleMenu = AdminMenu::get();
        $menu = self::menuMerge(array_merge($menu, $moduleMenu));
        if ($customOverwrite) {
            $menuCustom = modstart_config()->getArray('adminMenuCustom', []);
            if (!empty($menuCustom)) {
                $menuCustom = self::menuMerge($menuCustom);
                $menu = self::menuMergeCustom($menu, $menuCustom);
            }
        }
        if ($filter) {
            $menu = self::menuFilter($menu, $filter);
        }
        $menu = self::menuRuleMode($menu, $ruleMode);
        //
        //$menuCustomFlat = self::menuFlatUrl($menuCustom);
        //$menuAfterCustom = self::menuClean($menuAll, $menuCustomFlat);
        //return $menuAfterCustom;
        //$menuAll = array_merge($menuCustom, $menuAfterCustom);
        ////return $menuAll;
        //$menu = self::mergeMenu($menuAll, '', 1, $filter, $ruleMode);
        //return $menu;
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
            $adminRules = Session::get(Admin::ADMIN_RULES_SESSION_KEY);
            $adminUser = Session::get(Admin::ADMIN_USER_SESSION_KEY);
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
            $menu = self::menuMerge(array_merge($menu, $moduleMenu));
            //$menu = self::mergeMenu(array_merge($menu, $moduleMenu), '', 1, null, true);
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
