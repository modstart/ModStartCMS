<?php

namespace Overtrue\Socialite\Contracts;

use Overtrue\Socialite\User;

interface ProviderInterface
{
    public function redirect($redirectUrl = null);

    
    public function userFromCode($code);

    public function userFromToken($token);
}
