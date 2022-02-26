<?php


namespace ModStart\Admin\Event;


use ModStart\Core\Util\EventUtil;

class AdminUserLoginedEvent
{
    public $adminUserId;
    public $ip;
    public $ua;

    public static function fire($adminUserId, $ip, $ua)
    {
        $event = new AdminUserLoginedEvent();
        $event->adminUserId = $adminUserId;
        $event->ip = $ip;
        $event->ua = $ua;
        EventUtil::fire($event);
    }
}
