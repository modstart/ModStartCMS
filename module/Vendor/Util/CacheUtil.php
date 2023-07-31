<?php

namespace Module\Vendor\Util;

use Illuminate\Support\Facades\Cache;

/**
 * Class CacheUtil
 * @package Module\Vendor\Cache
 */
class CacheUtil
{
    /**
     * @return \Illuminate\Cache\Repository
     */
    private static function client()
    {
        return Cache::store();
    }

    public static function rememberForever($key, $callback)
    {
        return self::client()->rememberForever($key, $callback);
    }

    public static function remember($key, $seconds, $callback)
    {
        return self::client()->remember($key, intval($seconds / 60), $callback);
    }

    public static function forget($key)
    {
        return self::client()->forget($key);
    }

    public static function get($key)
    {
        return self::client()->get($key);
    }

    public static function put($key, $value, $seconds)
    {
        self::client()->put($key, $value, ceil($seconds / 60));
    }

    public static function forever($key, $value)
    {
        self::client()->forever($key, $value);
    }

    public static function executeInterval($key, $callback, $minutes = 5)
    {
        $cacheKey = 'Vendor:CacheInterval:' . $key;
        $c = intval(self::get($cacheKey));
        if ($c <= 0 || $c < time()) {
            self::put($cacheKey, time() + $minutes * 60, $minutes * 60);
            call_user_func($callback);
        }
    }
}
