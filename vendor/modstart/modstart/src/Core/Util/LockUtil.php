<?php


namespace ModStart\Core\Util;


use NinjaMutex\Lock\MySqlLock;
use NinjaMutex\MutexFabric;

class LockUtil
{
    static $instance = null;

    /**
     * @return MutexFabric
     */
    private static function instance()
    {
        if (null === self::$instance) {
            $mysqlLock = new MySqlLock(
                config('env.DB_USERNAME'),
                config('env.DB_PASSWORD'),
                config('env.DB_HOST')
            );
            $mutexFabric = new MutexFabric('mysql', $mysqlLock);
            self::$instance = $mutexFabric;
        }
        return self::$instance;
    }

    public static function acquire($name, $timeout = 60)
    {
        if (RedisUtil::isEnable()) {
            $key = "Lock:$name";
            if (RedisUtil::setnx($key, time() + $timeout)) {
                RedisUtil::expire($key, $timeout);
                return true;
            }
            $ts = RedisUtil::get($key);
            if ($ts < time()) {
                RedisUtil::delete($key);
                return self::acquire($name, $timeout);
            }
            return false;
        } else {
            if (self::instance()->get($name)->acquireLock($timeout)) {
                return true;
            }
        }
        return false;
    }

    public static function release($name)
    {
        if (RedisUtil::isEnable()) {
            $key = "Lock:$name";
            RedisUtil::delete($key);
        } else {
            self::instance()->get($name)->releaseLock();
        }
    }
}
