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
        $regex = str_replace('__x__star__', '[a-zA-Z0-9_]+', $regex);
        // echo "isWildMatch ==> $regex <-> $text\n";
        return preg_match($regex, $text);
    }
}
