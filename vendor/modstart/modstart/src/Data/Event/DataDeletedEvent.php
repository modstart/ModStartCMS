<?php


namespace ModStart\Data\Event;


use Illuminate\Support\Facades\Event;
use ModStart\Core\Util\EventUtil;

class DataDeletedEvent
{
    public $data;

    public static function fire($data)
    {
        $event = new static();
        $event->data = $data;
        EventUtil::fire($event);
    }

    public static function listen($callback)
    {
        Event::listen(DataDeletedEvent::class, function (DataDeletedEvent $event) use ($callback) {
            call_user_func($callback, $event);
        });
    }
}
