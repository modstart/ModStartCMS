<?php

namespace Overtrue\Socialite\Providers;

use Overtrue\Socialite\User;


class Google extends Base
{
    const NAME = 'google';
    protected $scopeSeparator = ' ';
    protected $scopes = [
        'https://www.googleapis.com/auth/userinfo.email',
        'https://www.googleapis.com/auth/userinfo.profile',
    ];

    protected function getAuthUrl()
    {
        return $this->buildAuthUrlFromBase('https://accounts.google.com/o/oauth2/v2/auth');
    }

    protected function getTokenUrl()
    {
        return 'https://www.googleapis.com/oauth2/v4/token';
    }

    
    public function tokenFromCode($code)
    {
        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
            'form_params' => $this->getTokenFields($code),
        ]);

        return $this->normalizeAccessTokenResponse($response->getBody());
    }

    
    protected function getTokenFields($code)
    {
        return parent::getTokenFields($code) + ['grant_type' => 'authorization_code'];
    }

    
    protected function getUserByToken($token, $query = [])
    {
        $response = $this->getHttpClient()->get('https://www.googleapis.com/userinfo/v2/me', [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer '.$token,
            ],
        ]);

        return \json_decode($response->getBody(), true) ?? [];
    }

    
    protected function mapUserToObject($user)
    {
        return new User([
            'id' => $user['id'] ?? null,
            'username' => $user['email'] ?? null,
            'nickname' => $user['name'] ?? null,
            'name' => $user['name'] ?? null,
            'email' => $user['email'] ?? null,
            'avatar' => $user['picture'] ?? null,
        ]);
    }
}
