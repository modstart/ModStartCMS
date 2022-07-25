<?php


namespace ModStart\Admin\Event;


use ModStart\Core\Util\EventUtil;

class AdminUserLoginAttemptEvent
{
    public $adminUserId;
    public $ip;
    public $ua;

    public static function fire($adminUserId, $ip, $ua)
    {
        $event = new AdminUserLoginAttemptEvent();
        $event->adminUserId = $adminUserId;
        $event->ip = $ip;
        $event->ua = $ua;
        EventUtil::fire($event);
    }
}
