<?php

namespace Module\Member\Events;


use ModStart\Core\Util\EventUtil;

class MemberUserDeletedEvent
{
    public $memberUserId;

    public static function fire($memberUserId)
    {
        $event = new static();
        $event->memberUserId = $memberUserId;
        EventUtil::fire($event);
    }

}
