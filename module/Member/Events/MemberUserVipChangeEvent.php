<?php


namespace Module\Member\Events;


use ModStart\Core\Util\EventUtil;

class MemberUserVipChangeEvent
{
    public $memberUserId;
    public $fromVipId;
    public $toVipId;

    public static function fire($memberUserId, $fromVipId, $toVipId)
    {
        $event = new static();
        $event->memberUserId = $memberUserId;
        $event->fromVipId = $fromVipId;
        $event->toVipId = $toVipId;
        EventUtil::fire($event);
    }
}
