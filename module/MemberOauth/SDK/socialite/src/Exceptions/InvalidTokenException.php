<?php

namespace Overtrue\Socialite\Exceptions;

class InvalidTokenException extends Exception
{
    public $token;

    
    public function __construct($message, $token)
    {
        parent::__construct($message, -1);

        $this->token = $token;
    }
}
