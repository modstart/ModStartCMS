<?php

namespace Module\Cms\Event;


class PostLikedEvent
{
    public $postId;
    public $memberUserId;

    public function __construct($postId, $memberUserId)
    {
        $this->postId = $postId;
        $this->memberUserId = $memberUserId;
    }


}
