<?php

namespace Module\Member\Events;

class MemberUserLogoutEvent
{
    public $memberUserId;

    public function __construct($memberUserId)
    {
        $this->memberUserId = $memberUserId;
    }


}
