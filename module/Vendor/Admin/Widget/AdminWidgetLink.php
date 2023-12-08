<?php


namespace Module\Vendor\Admin\Widget;


use Module\Vendor\Type\AdminWidgetLinkType;

class AdminWidgetLink
{
    private static $list = [];

    public static function register($closure)
    {
        self::$list[] = $closure;
    }

    public static function get()
    {
        $results = [];
        foreach (self::$list as $item) {
            if ($item instanceof \Closure) {
                $result = call_user_func($item);
                if (!empty($result)) {
                    if (isset($result['title']) && isset($result['list'])) {
                        $results[] = $result;
                    } else {
                        $results = array_merge($results, $result);
                    }
                }
            } else {
                $results[] = $item;
            }
        }
        foreach ($results as $i => $v) {
            if (!isset($v['type'])) {
                $results[$i]['type'] = AdminWidgetLinkType::WEB;
            }
        }
        $resultMap = [];
        foreach ($results as $k => $v) {
            $key = $v['type'] . '-' . $v['title'];
            if (isset($resultMap[$key])) {
                $resultMap[$key]['list'] = array_merge($resultMap[$key]['list'], $v['list']);
            } else {
                $resultMap[$key] = $v;
            }
        }
        return array_values($resultMap);
    }

    public static function build($groupName, $titleLinks, $type = null)
    {
        if (is_null($type)) {
            $type = AdminWidgetLinkType::WEB;
        }
        if (empty($titleLinks)) {
            return null;
        }
        return [
            'title' => $groupName,
            'type' => $type,
            'list' => array_filter(array_map(function ($item) {
                return $item ? [
                    'title' => $item[0],
                    'link' => $item[1],
                ] : null;
            }, $titleLinks))
        ];
    }

    public static function buildMobileWithPrefix($linkPrefix, $groupName, $titleLinks)
    {
        $titleLinks = array_map(function ($item) use ($linkPrefix) {
            return [$item[0], $linkPrefix . $item[1]];
        }, $titleLinks);
        return self::build($groupName, $titleLinks, AdminWidgetLinkType::MOBILE);
    }
}
