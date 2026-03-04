<?php


namespace ModStart\Core\Util;


/**
 * @Util 平台工具
 */
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

    /**
     * @Util 判断当前是否为 Windows 平台
     * @return bool
     */
    public static function isWindows()
    {
        return substr(self::name(), 0, 3) == "WIN";
    }

    /**
     * @Util 判断当前是否为 macOS 平台
     * @return bool
     */
    public static function isOsx()
    {
        return self::name() == 'DARWIN';
    }

    /**
     * @Util 判断当前是否为 Linux 平台
     * @return bool
     */
    public static function isLinux()
    {
        return self::name() == 'LINUX';
    }

    /**
     * @Util 判断当前平台是否属于指定类型
     * @param $types array 平台类型数组，可选値 windows/linux/osx/unknown
     * @return bool
     */
    public static function isType($types)
    {
        return in_array(self::type(), $types);
    }

    /**
     * @Util 获取当前平台类型字符串
     * @return string windows|linux|osx|unknown
     */
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

    /**
     * @Util 获取系统总内存大小（字节）
     * @return int
     */
    public static function memoryTotal()
    {
        $memoryInfo = self::memoryInfo();
        return $memoryInfo['total'];
    }

    /**
     * @Util 获取系统已使用内存大小（字节）
     * @return int
     */
    public static function memoryUsed()
    {
        $memoryInfo = self::memoryInfo();
        return $memoryInfo['used'];
    }

}
