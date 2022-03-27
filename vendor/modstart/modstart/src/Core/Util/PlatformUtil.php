<?php


namespace ModStart\Core\Util;


class PlatformUtil
{
    const WINDOWS = 'windows';
    const LINUX = 'linux';
    const OSX = 'osx';
    const UNKNOWN = 'unknown';

    private static function name()
    {
        return strtoupper(PHP_OS);
    }

    public static function isWindows()
    {
        return substr(self::name(), 0, 3) == "WIN";
    }

    public static function isOsx()
    {
        return self::name() == 'DARWIN';
    }

    public static function isLinux()
    {
        return self::name() == 'LINUX';
    }

    public static function isType($types)
    {
        return in_array(self::type(), $types);
    }

    public static function type()
    {
        if (self::isOsx()) {
            return self::OSX;
        }
        if (self::isWindows()) {
            return self::WINDOWS;
        }
        if (self::isLinux()) {
            return self::LINUX;
        }
        return self::UNKNOWN;
    }
}
