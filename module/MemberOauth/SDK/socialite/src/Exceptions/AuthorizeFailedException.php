<?php

namespace Overtrue\Socialite\Exceptions;

class AuthorizeFailedException extends Exception
{
    public $body;

    
    public function __construct($message, $body)
    {
        parent::__construct($message, -1);

        $this->body = (array) $body;
    }
}
