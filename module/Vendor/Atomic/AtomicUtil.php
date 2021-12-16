<?php

namespace Module\Vendor\Atomic;

use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Util\RandomUtil;
use ModStart\Core\Util\RedisUtil;

/**
 * Class AtomicUtil
 *
 * product onece
 * comsume some times
 *
 * example:
 * - captcha error count
 */
class AtomicUtil
{
    public static function produce($name, $value, $expire = 3600)
    {
        if (RedisUtil::isEnable()) {
            $hash = "Atomic:$name";
            RedisUtil::set($hash, $value);
            RedisUtil::expire($hash, $expire);
        } else {
            if (ModelUtil::exists('atomic', ['name' => $name,])) {
                ModelUtil::update('atomic', ['name' => $name,], ['value' => $value, 'expire' => time() + $expire]);
            } else {
                ModelUtil::insert('atomic', ['name' => $name, 'value' => $value, 'expire' => time() + $expire]);
            }
            if (RandomUtil::percent(20)) {
                ModelUtil::delete('atomic', 'expire', '<', time());
            }
        }
    }

    public static function consume($name)
    {
        if (RedisUtil::isEnable()) {
            $hash = "Atomic:$name";
            if (RedisUtil::decr($hash) >= 0) {
                return true;
            }
            return false;
        } else {
            if (RandomUtil::percent(20)) {
                ModelUtil::delete('atomic', 'expire', '<', time());
            }
            ModelUtil::transactionBegin();
            $atomic = ModelUtil::getWithLock('atomic', ['name' => $name]);
            if (empty($atomic)) {
                ModelUtil::transactionCommit();
                return false;
            }
            if ($atomic['expire'] < time() || $atomic['value'] < 0) {
                ModelUtil::delete('atomic', ['name' => $name]);
                ModelUtil::transactionCommit();
                return false;
            }
            ModelUtil::update('atomic', ['name' => $name], ['value' => $atomic['value'] - 1]);
            ModelUtil::transactionCommit();
            return true;
        }
    }

    public static function remove($name)
    {
        if (RedisUtil::isEnable()) {
            $hash = "Atomic:$name";
            RedisUtil::delete($hash);
        } else {
            ModelUtil::delete('atomic', ['name' => $name]);
        }
    }
}