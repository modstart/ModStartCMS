<?php

namespace Module\CmsWriter\Event;


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
