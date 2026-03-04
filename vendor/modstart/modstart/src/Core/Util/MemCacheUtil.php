<?php

namespace ModStart\Core\Util;


/**
 * @Util 进程内内存缓存工具
 */
class MemCacheUtil
{
    /**
     *  key => [
     *      0 => <expire timestamp> 0 means no expire
     *      1 => <object>
     *  ]
     */
    private static $pool = [];

    /**
     * @Util 获取缓存内容，若不存在则通过回调生成并存入缓存
     * @param $key string 缓存键
     * @param $callback callable 缓存未命中时的回调函数
     * @param $expire int 过期时间（秒），0 表示永不过期
     * @return mixed
     */
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

    /**
     * @Util 获取缓存内容
     * @param $key string 缓存键
     * @return mixed|null 缓存不存在或已过期时返回 null
     */
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

    /**
     * @Util 写入缓存
     * @param $key string 缓存键
     * @param $value mixed 缓存内容
     * @param $expire int 过期时间（秒），0 表示永不过期
     * @return void
     */
    public static function put($key, $value, $expire = 0)
    {
        self::$pool[$key] = [
            $expire > 0 ? time() + $expire : 0,
            $value
        ];
    }

    /**
     * @Util 删除缓存
     * @param $key string 缓存键
     * @return void
     */
    public static function forget($key)
    {
        if (array_key_exists($key, self::$pool)) {
            unset(self::$pool[$key]);
        }
    }
}
