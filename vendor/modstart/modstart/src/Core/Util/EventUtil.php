<?php


namespace ModStart\Core\Util;


use Illuminate\Support\Facades\Event;

/**
 * @Util 事件操作
 */
class EventUtil
{
    /**
     * @Util 触发一个Laravel事件，兼容了不同版本
     * @param $event object
     */
    public static function fire($event)
    {
        if (PHP_VERSION_ID >= 80000) {
            Event::dispatch($event);
        } else {
            Event::fire($event);
        }
    }

    /**
     * @Util 监听一个Laravel事件
     * @param $eventClass string 事件类名
     * @param $callback callable 回调函数
     */
    public static function listen($eventClass, $callback)
    {
        Event::listen($eventClass, function ($event) use ($callback) {
            call_user_func($callback, $event);
        });
    }
}
