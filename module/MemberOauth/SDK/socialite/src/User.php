<?php

namespace Overtrue\Socialite;

use ArrayAccess;
use JsonSerializable;
use Overtrue\Socialite\Contracts\ProviderInterface;
use Overtrue\Socialite\Contracts\UserInterface;
use Overtrue\Socialite\Traits\HasAttributes;

class User implements ArrayAccess, UserInterface, JsonSerializable, \Serializable
{
    use HasAttributes;

    
    protected $provider;

    public function __construct($attributes, $provider = null)
    {
        $this->attributes = $attributes;
        $this->provider = $provider;
    }

    public function getId()
    {
        return !empty($this->getAttribute('id')) ?$this->getAttribute('id'): $this->getEmail();
    }

    public function getNickname()
    {
        return !empty($this->getAttribute('nickname')) ?$this->getAttribute('nickname'): $this->getName();
    }

    public function getName()
    {
        return $this->getAttribute('name');
    }

    public function getEmail()
    {
        return $this->getAttribute('email');
    }

    public function getAvatar()
    {
        return $this->getAttribute('avatar');
    }

    public function setAccessToken($token)
    {
        $this->setAttribute('access_token', $token);

        return $this;
    }

    public function getAccessToken()
    {
        return $this->getAttribute('access_token');
    }

    public function setRefreshToken($refreshToken)
    {
        $this->setAttribute('refresh_token', $refreshToken);

        return $this;
    }

    public function getRefreshToken()
    {
        return $this->getAttribute('refresh_token');
    }

    public function setExpiresIn($expiresIn)
    {
        $this->setAttribute('expires_in', $expiresIn);

        return $this;
    }

    public function getExpiresIn()
    {
        return $this->getAttribute('expires_in');
    }

    public function setRaw($user)
    {
        $this->setAttribute('raw', $user);

        return $this;
    }

    public function getRaw()
    {
        return $this->getAttribute('raw');
    }

    public function setTokenResponse($response)
    {
        $this->setAttribute('token_response', $response);

        return $this;
    }

    public function getTokenResponse()
    {
        return $this->getAttribute('token_response');
    }

    public function jsonSerialize()
    {
        return $this->attributes;
    }

    public function serialize()
    {
        return serialize($this->attributes);
    }

    public function unserialize($serialized)
    {
        $d = unserialize($serialized);
        $this->attributes = !empty($d) ?$d: [];
    }

    
    public function getProvider()
    {
        return $this->provider;
    }

    
    public function setProvider(\Overtrue\Socialite\Contracts\ProviderInterface $provider)
    {
        $this->provider = $provider;

        return $this;
    }
}
