<?php

namespace Module\Member\Events;


class MemberUserRegisteredEvent
{
    public $memberUserId;

    public function __construct($memberUserId)
    {
        $this->memberUserId = $memberUserId;
    }

}