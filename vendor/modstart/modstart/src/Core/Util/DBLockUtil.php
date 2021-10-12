<?php

namespace ModStart\Core\Util;

use NinjaMutex\Lock\MySqlLock;
use NinjaMutex\MutexFabric;

class DBLockUtil
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

    public static function acquire($name, $timeout = null)
    {
        if (self::instance()->get($name)->acquireLock($timeout)) {
            return true;
        }
        return false;
    }

    public static function release($name)
    {
        self::instance()->get($name)->releaseLock();
    }
}
