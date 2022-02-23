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
}
