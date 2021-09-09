<?php


namespace ModStart\Admin\Config;


class AdminConfig
{
    private static $config = null;

    private static function init()
    {
        self::$config['demoId'] = config('env.ADMIN_DEMO_USER_ID', 0);
        self::$config['founderId'] = config('env.ADMIN_FOUNDER_ID', 0);
    }

    public static function set($key, $value)
    {
        if (null === self::$config) {
            self::init();
        }
        self::$config[$key] = $value;
    }

    public static function get($key = null, $default = null)
    {
        if (null === $key) {
            return self::$config;
        }
        if (isset($config[$key])) {
            return self::$config[$key];
        }
        return $default;
    }
}
