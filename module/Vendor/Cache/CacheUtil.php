<?php

namespace Module\Vendor\Cache;

use Illuminate\Support\Facades\Cache;

class CacheUtil
{
    
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
}