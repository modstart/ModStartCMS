<?php

namespace Module\Member\Events;


class MemberUserUpdatedEvent
{
    public $memberUserId;
        public $type;

    public function __construct($memberUserId, $type)
    {
        $this->memberUserId = $memberUserId;
        $this->type = $type;
    }

}