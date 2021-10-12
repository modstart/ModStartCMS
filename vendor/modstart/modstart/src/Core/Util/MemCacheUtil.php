<?php

namespace ModStart\Core\Util;


class MemCacheUtil
{
    /**
     *  key => [
     *      0 => <expire timestamp> 0 means no expire
     *      1 => <object>
     *  ]
     */
    private static $pool = [];

    public static function remember($key, $callback, $expire = 10)
    {
        if (array_key_exists($key, self::$pool)) {
            $v = self::$pool[$key];
            if ($v[0] === 0 || $v[0] < time()) {
                return $v[1];
            }
        }
        $value = $callback();
        self::put($key, $value, $expire);
        return $value;
    }

    public static function get($key)
    {
        if (array_key_exists($key, self::$pool)) {
            $v = self::$pool[$key];
            if ($v[0] === 0 || $v[0] < time()) {
                return $v[1];
            }
        }
        return null;
    }

    public static function put($key, $value, $expire = 0)
    {
        self::$pool[$key] = [
            $expire > 0 ? time() + $expire : 0,
            $value
        ];
    }

    public static function forget($key)
    {
        if (array_key_exists($key, self::$pool)) {
            unset(self::$pool[$key]);
        }
    }
}
