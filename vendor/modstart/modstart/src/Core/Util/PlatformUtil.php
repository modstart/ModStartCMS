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

    private static function memoryInfo()
    {
        $info = [
            'total' => 0,
            'used' => 0,
        ];
        if (self::isLinux()) {
            $memoryInfo = file_get_contents('/proc/meminfo');
            foreach (explode("\n", $memoryInfo) as $line) {
                if (preg_match('/MemTotal:\s+(\d+)\skB/', $line, $matches)) {
                    $info['total'] = $matches[1] * 1024;
                } else if (preg_match('/MemAvailable:\s+(\d+)\skB/', $line, $matches)) {
                    $info['used'] = $info['total'] - $matches[1] * 1024;
                }
            }
        } else if (self::isWindows()) {
            // todo
        } else if (self::isOsx()) {
            // todo
        }
        return $info;
    }

    public static function memoryTotal()
    {
        $memoryInfo = self::memoryInfo();
        return $memoryInfo['total'];
    }

    public static function memoryUsed()
    {
        $memoryInfo = self::memoryInfo();
        return $memoryInfo['used'];
    }

}
