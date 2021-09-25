<?php


namespace Module\Nav\Type;


class NavLink
{
    public static function generate($linkOrNav, $param = [])
    {
        if (empty($linkOrNav)) {
            return '';
        }
        if (is_array($linkOrNav)) {
            $linkOrNav = isset($linkOrNav['link']) ? $linkOrNav['link'] : null;
        }
        if (!empty($param)) {
            $keys = array_map(function ($item) {
                return '{' . $item . '}';
            }, array_keys($param));
            $linkOrNav = str_replace($keys, array_values($param), $linkOrNav);
        }
        return $linkOrNav;
    }
}
