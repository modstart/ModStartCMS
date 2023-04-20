<?php


namespace ModStart\Core\Util;


use Illuminate\Support\Facades\Session;

class SessionUtil
{
    public static function id()
    {
        return Session::getId();
    }

    public static function get($sessionId, $key)
    {
        $oldSessionId = Session::getId();
        Session::save();

        Session::setId($sessionId);
        Session::start();
        $value = Session::get($key);
        Session::clear();

        Session::setId($oldSessionId);
        Session::start();

        return $value;
    }

    public static function put($sessionId, $key, $value)
    {
        $oldSessionId = Session::getId();
        Session::save();

        Session::setId($sessionId);
        Session::start();
        Session::put($key, $value);
        Session::save();
        Session::clear();

        Session::setId($oldSessionId);
        Session::start();
    }

    public static function forget($sessionId, $key)
    {
        $oldSessionId = Session::getId();
        Session::save();

        Session::setId($sessionId);
        Session::start();
        Session::forget($key);
        Session::save();
        Session::clear();

        Session::setId($oldSessionId);
        Session::start();
    }

    public static function clear($sessionId)
    {
        $oldSessionId = Session::getId();
        Session::save();

        Session::setId($sessionId);
        Session::start();
        Session::flush();
        Session::save();
        Session::clear();

        Session::setId($oldSessionId);
        Session::start();
    }
}
