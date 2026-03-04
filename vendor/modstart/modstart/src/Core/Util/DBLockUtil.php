<?php

namespace ModStart\Core\Util;

use NinjaMutex\Lock\MySqlLock;
use NinjaMutex\MutexFabric;

/**
 * Class DBLockUtil
 * @package ModStart\Core\Util
 * @deprecated delete at 2023-11-30
 */
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

    /**
     * @Util 申请一个数据库互斥锁
     * @param $name string 锁名称
     * @param $timeout int 超时时间（秒）
     * @return bool 是否成功
     */
    public static function acquire($name, $timeout = null)
    {
        if (self::instance()->get($name)->acquireLock($timeout)) {
            return true;
        }
        return false;
    }

    /**
     * @Util 释放一个数据库互斥锁
     * @param $name string 锁名称
     * @return void
     */
    public static function release($name)
    {
        self::instance()->get($name)->releaseLock();
    }
}
