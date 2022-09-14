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

    public static function atomicConsume($name)
    {
        return AtomicUtil::consume("$name:" . Session::getId());
    }

    public static function atomicRemove($name)
    {
        AtomicUtil::remove("$name:" . Session::getId());
    }
}
