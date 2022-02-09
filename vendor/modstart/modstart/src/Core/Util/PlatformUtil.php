<?php


namespace ModStart\Core\Util;


class PlatformUtil
{
    public static function isWindows()
    {
        return strtoupper(substr(PHP_OS, 0, 3)) == "WIN";
    }
}