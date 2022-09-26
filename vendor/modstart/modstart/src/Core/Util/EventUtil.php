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
}
