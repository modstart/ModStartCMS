<?php


namespace ModStart\Admin\Event;


use ModStart\Core\Util\EventUtil;

class AdminUserLoginFailedEvent
{
    public $adminUserId;
    public $username;
    public $ip;
    public $ua;

    public static function fire($adminUserId, $username, $ip, $ua)
    {
        $event = new AdminUserLoginFailedEvent();
        $event->adminUserId = $adminUserId;
        $event->username = $username;
        $event->ip = $ip;
        $event->ua = $ua;
        EventUtil::fire($event);
    }
}
