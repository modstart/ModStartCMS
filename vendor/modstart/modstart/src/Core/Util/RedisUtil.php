<?php

namespace ModStart\Core\Util;

use Illuminate\Support\Facades\Redis;

class RedisUtil
{
    public static function isEnable()
    {
        return !!config('env.REDIS_HOST');
    }

    public static function isEnableSuccess()
    {
        if (!self::isEnable()) {
            return false;
        }
        try {
            $client = Redis::connection('default');
            $client->ping();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @return \Predis\Client
     */
    public static function client()
    {
        static $client = null;
        if (null === $client) {
            $client = Redis::connection('default');
        }
        return $client;
    }

    public static function get($key)
    {
        $value = self::client()->get($key);
        return $value;
    }

    public static function getObject($key)
    {
        $value = self::client()->get($key);
        return @json_decode($value, true);
    }

    public static function set($key, $value)
    {
        self::client()->set($key, $value);
    }

    public static function setnx($key, $value)
    {
        return self::client()->setnx($key, $value);
    }

    public static function setex($key, $value, $expireSecond)
    {
        self::client()->setex($key, $expireSecond, $value);
    }

    public static function setexObject($key, $value, $expireSecond)
    {
        self::client()->setex($key, $expireSecond, SerializeUtil::jsonEncode($value));
    }

    public static function delete($key)
    {
        self::client()->del([$key]);
    }

    public static function incr($key)
    {
        self::client()->incr($key);
    }

    public static function decr($key)
    {
        return self::client()->decr($key);
    }

    public static function expire($key, $seconds)
    {
        self::client()->expire($key, $seconds);
    }
}
