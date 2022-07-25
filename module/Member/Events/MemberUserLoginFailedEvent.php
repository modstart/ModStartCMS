<?php


namespace Module\Member\Events;


use ModStart\Core\Util\EventUtil;

class MemberUserLoginFailedEvent
{
    public $memberUserId;
    public $username;
    public $ip;
    public $ua;

    public static function fire($memberUserId, $username, $ip, $ua)
    {
        $event = new MemberUserLoginFailedEvent();
        $event->memberUserId = $memberUserId;
        $event->username = $username;
        $event->ip = $ip;
        $event->ua = $ua;
        EventUtil::fire($event);
    }
}
