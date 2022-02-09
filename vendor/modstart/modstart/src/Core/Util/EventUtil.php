<?php


namespace ModStart\Core\Util;


use Illuminate\Support\Facades\Event;

class EventUtil
{
    public static function fire($event)
    {
        if (PHP_VERSION_ID >= 80000) {
            Event::dispatch($event);
        } else {
            Event::fire($event);
        }
    }
}
