<?php


namespace Module\Member\Events;


use ModStart\Core\Util\EventUtil;

class MemberUserLoginAttemptEvent
{
    public $memberUserId;
    public $ip;
    public $ua;

    public static function fire($memberUserId, $ip, $ua)
    {
        $event = new MemberUserLoginAttemptEvent();
        $event->memberUserId = $memberUserId;
        $event->ip = $ip;
        $event->ua = $ua;
        EventUtil::fire($event);
    }
}
