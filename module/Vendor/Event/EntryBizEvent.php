<?php

namespace Module\Vendor\Event;

use ModStart\Core\Util\EventUtil;

class EntryBizEvent
{
    public $name;
    public $param;

    public static function fire($name, $param = [])
    {
        $event = new static();
        $event->name = $name;
        $event->param = $param;
        EventUtil::fire($event);
    }
}
