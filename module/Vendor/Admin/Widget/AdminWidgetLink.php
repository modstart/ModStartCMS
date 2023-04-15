<?php


namespace Module\Vendor\Admin\Widget;


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
        $resultMap = [];
        foreach ($results as $k => $v) {
            if (isset($resultMap[$v['title']])) {
                $resultMap[$v['title']]['list'] = array_merge($resultMap[$v['title']]['list'], $v['list']);
            } else {
                $resultMap[$v['title']] = $v;
            }
        }
        return array_values($resultMap);
    }

    public static function build($groupName, $titleLinks)
    {
        if (empty($titleLinks)) {
            return null;
        }
        return [
            'title' => $groupName,
            'list' => array_filter(array_map(function ($item) {
                return $item ? [
                    'title' => $item[0],
                    'link' => $item[1],
                ] : null;
            }, $titleLinks))
        ];
    }
}
