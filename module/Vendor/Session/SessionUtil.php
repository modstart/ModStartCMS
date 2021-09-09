<?php


namespace Module\Vendor\Session;

use Illuminate\Support\Facades\Session;
use Module\Vendor\Atomic\AtomicUtil;

class SessionUtil
{
    public static function atomicProduce($name, $value, $expire = 3600)
    {
        AtomicUtil::produce("$name:" . Session::getId(), $value, $expire);
    }

    public static function atomicConsume($name, $token = null)
    {
        return AtomicUtil::consume("$name:" . Session::getId());
    }

    public static function atomicRemove($name, $token = null)
    {
        AtomicUtil::remove("$name:" . Session::getId());
    }
}