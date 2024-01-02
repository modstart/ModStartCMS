<?php


namespace Module\Vendor\Util;

use Illuminate\Support\Facades\Session;

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

    public static function startForWebFullUrl($redirect)
    {
        return modstart_web_full_url('session', [
            'api_token' => Session::getId(),
            'redirect' => $redirect,
        ]);
    }
}
