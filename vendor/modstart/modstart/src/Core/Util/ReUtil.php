<?php

namespace ModStart\Core\Util;


class ReUtil
{
    public static function group($regex, $text, $groupIndex)
    {
        try {
            if (preg_match($regex, $text, $mat)) {
                return $mat[$groupIndex];
            }
        } catch (\Exception $e) {
        }
        return null;
    }

    public static function group0($regex, $text)
    {
        try {
            return self::group($regex, $text, 0);
        } catch (\Exception $e) {
        }
        return null;
    }

    public static function group1($regex, $text)
    {
        try {
            return self::group($regex, $text, 1);
        } catch (\Exception $e) {
        }
        return null;
    }

    public static function isWildMatch($wild, $text)
    {
        $wild = str_replace('*', '__x__star__', $wild);
        $regex = '/^' . preg_quote($wild, '/') . '$/';
        $regex = str_replace('__x__star__', '.*', $regex);
        // echo "isWildMatch ==> $regex <-> $text\n";
        return preg_match($regex, $text);
    }

    public static function replace($content, $regex, $callback)
    {
        preg_match_all($regex, $content, $mat);
        foreach ($mat[0] as $i => $v) {
            $row = array_map(function ($o) use ($i, $mat) {
                return $o[$i];
            }, $mat);
            $replace = call_user_func_array($callback, [$row]);
            $content = str_replace($v, $replace, $content);
        }
        return $content;
    }
}
