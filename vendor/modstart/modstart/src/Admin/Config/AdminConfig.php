<?php


namespace ModStart\Admin\Config;


class AdminConfig
{
    private static $config = null;

    private static function init()
    {
        self::$config = [];
        self::$config['demoId'] = config('env.ADMIN_DEMO_USER_ID', 0);
        self::$config['founderId'] = config('env.ADMIN_FOUNDER_ID', 1);
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
        if (null === self::$config) {
            self::init();
        }
        if (null === $key) {
            return self::$config;
        }
        if (isset(self::$config[$key])) {
            return self::$config[$key];
        }
        return $default;
    }
}
