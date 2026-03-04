<?php

namespace ModStart\Core\Util;

use Illuminate\Support\Facades\Redis;

/**
 * @Util Redis 操作工具
 */
class RedisUtil
{
    /**
     * @Util 判断 Redis 是否已配置（检查环境变量）
     * @return bool
     */
    public static function isEnable()
    {
        return !!config('env.REDIS_HOST');
    }

    /**
     * @Util 判断 Redis 是否已配置且能成功连接
     * @return bool
     */
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

    /**
     * @Util 获取 Redis 键的字符串値
     * @param $key string
     * @return string|null
     */
    public static function get($key)
    {
        $value = self::client()->get($key);
        return $value;
    }

    /**
     * @Util 获取 Redis 键的对象値（JSON 解码为数组）
     * @param $key string
     * @return array|null
     */
    public static function getObject($key)
    {
        $value = self::client()->get($key);
        return @json_decode($value, true);
    }

    /**
     * @Util 设置 Redis 键的字符串値
     * @param $key string
     * @param $value string
     * @return void
     */
    public static function set($key, $value)
    {
        self::client()->set($key, $value);
    }

    /**
     * @Util 当键不存在时设置字符串値（Set if Not eXists）
     * @param $key string
     * @param $value string
     * @return bool 如果唯一设置成功返回 true
     */
    public static function setnx($key, $value)
    {
        return self::client()->setnx($key, $value);
    }

    /**
     * @Util 设置带过期时间的字符串値
     * @param $key string
     * @param $value string
     * @param $expireSecond int 过期时间（秒）
     * @return void
     */
    public static function setex($key, $value, $expireSecond)
    {
        self::client()->setex($key, $expireSecond, $value);
    }

    /**
     * @Util 设置带过期时间的对象値（自动 JSON 编码）
     * @param $key string
     * @param $value mixed
     * @param $expireSecond int 过期时间（秒）
     * @return void
     */
    public static function setexObject($key, $value, $expireSecond)
    {
        self::client()->setex($key, $expireSecond, SerializeUtil::jsonEncode($value));
    }

    /**
     * @Util 删除 Redis 键
     * @param $key string
     * @return void
     */
    public static function delete($key)
    {
        self::client()->del([$key]);
    }

    /**
     * @Util 将 Redis 键的値自增 1
     * @param $key string
     * @return void
     */
    public static function incr($key)
    {
        self::client()->incr($key);
    }

    /**
     * @Util 将 Redis 键的値自减 1
     * @param $key string
     * @return int 自减后的値
     */
    public static function decr($key)
    {
        return self::client()->decr($key);
    }

    /**
     * @Util 设置 Redis 键的过期时间
     * @param $key string
     * @param $seconds int 过期时间（秒）
     * @return void
     */
    public static function expire($key, $seconds)
    {
        self::client()->expire($key, $seconds);
    }
}
