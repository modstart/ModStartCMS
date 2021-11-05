<?php

namespace Overtrue\Socialite\Providers;

use Overtrue\Socialite\User;

class Outlook extends Base
{
    const NAME = 'outlook';
    protected $scopes = ['User.Read'];
    protected $scopeSeparator = ' ';

    protected function getAuthUrl()
    {
        return $this->buildAuthUrlFromBase('https://login.microsoftonline.com/common/oauth2/v2.0/authorize');
    }

    protected function getTokenUrl()
    {
        return 'https://login.microsoftonline.com/common/oauth2/v2.0/token';
    }

    
    protected function getUserByToken($token, $query = [])
    {
        $response = $this->getHttpClient()->get(
            'https://graph.microsoft.com/v1.0/me',
            ['headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer '.$token,
            ],
            ]
        );

        return \json_decode($response->getBody()->getContents(), true) ?? [];
    }

    
    protected function mapUserToObject($user)
    {
        return new User([
            'id' => $user['id'] ?? null,
            'nickname' => null,
            'name' => $user['displayName'] ?? null,
            'email' => $user['userPrincipalName'] ?? null,
            'avatar' => null,
        ]);
    }

    
    protected function getTokenFields($code)
    {
        return parent::getTokenFields($code) + [
            'grant_type' => 'authorization_code',
        ];
    }
}
