<?php


namespace ModStart\Data\Event;


use ModStart\Core\Util\EventUtil;

class DataFileUploadedEvent
{
    public $driver;
    public $category;
    public $path;

    public static function fire($driver, $category, $path)
    {
        $event = new DataFileUploadedEvent();
        $event->driver = $driver;
        $event->category = $category;
        $event->path = $path;
        EventUtil::fire($event);
    }

    private static $param = [];

    public static function setParam($key, $value)
    {
        self::$param[$key] = $value;
    }

    public static function forgetParam($key)
    {
        unset(self::$param[$key]);
    }

    public static function getParam($key)
    {
        return isset(self::$param[$key]) ? self::$param[$key] : null;
    }

}
