<?php


namespace ModStart\Core\Util;


use Illuminate\Support\Facades\Session;

class SessionUtil
{
    public static function id()
    {
        return Session::getId();
    }

    private static function getDataFromSession($sessionId)
    {
        $sessionData = Session::getHandler()->read($sessionId);
        $data = @unserialize($sessionData);
        if (!is_array($data)) {
            $data = [];
        }
        return $data;
    }

    private static function saveDataToSession($sessionId, $data)
    {
        $sessionData = serialize($data);
        Session::getHandler()->write($sessionId, $sessionData);
    }

    public static function get($sessionId, $key)
    {
        $data = self::getDataFromSession($sessionId);
        return isset($data[$key]) ? $data[$key] : null;

        // $oldSessionId = Session::getId();
        // Session::save();
        //
        // Session::setId($sessionId);
        // Session::start();
        // $value = Session::get($key);
        // Session::clear();
        //
        // Session::setId($oldSessionId);
        // Session::start();
        //
        // return $value;
    }

    public static function put($sessionId, $key, $value)
    {
        $data = self::getDataFromSession($sessionId);
        $data[$key] = $value;
        self::saveDataToSession($sessionId, $data);

        // $oldSessionId = Session::getId();
        // Session::save();
        //
        // Session::setId($sessionId);
        // Session::start();
        // Session::put($key, $value);
        // Session::save();
        // Session::clear();
        //
        // Session::setId($oldSessionId);
        // Session::start();
    }

    public static function forget($sessionId, $key)
    {
        $data = self::getDataFromSession($sessionId);
        unset($data[$key]);
        self::saveDataToSession($sessionId, $data);

        // $oldSessionId = Session::getId();
        // Session::save();
        //
        // Session::setId($sessionId);
        // Session::start();
        // Session::forget($key);
        // Session::save();
        // Session::clear();
        //
        // Session::setId($oldSessionId);
        // Session::start();
    }

    public static function clear($sessionId)
    {
        self::saveDataToSession($sessionId, []);

        // $oldSessionId = Session::getId();
        // Session::save();
        //
        // Session::setId($sessionId);
        // Session::start();
        // Session::flush();
        // Session::save();
        // Session::clear();
        //
        // Session::setId($oldSessionId);
        // Session::start();
    }
}
