<?php

namespace Overtrue\Socialite\Providers;

use Overtrue\Socialite\User;

class GitHub extends Base
{
    const NAME = 'github';
    protected $scopes = ['read:user'];

    protected function getAuthUrl()
    {
        return $this->buildAuthUrlFromBase('https://github.com/login/oauth/authorize');
    }

    protected function getTokenUrl()
    {
        return 'https://github.com/login/oauth/access_token';
    }

    
    protected function getUserByToken($token)
    {
        $userUrl = 'https://api.github.com/user';

        $response = $this->getHttpClient()->get(
            $userUrl,
            $this->createAuthorizationHeaders($token)
        );

        $user = json_decode($response->getBody(), true);

        if (in_array('user:email', $this->scopes)) {
            $user['email'] = $this->getEmailByToken($token);
        }

        return $user;
    }

    
    protected function getEmailByToken($token)
    {
        $emailsUrl = 'https://api.github.com/user/emails';

        try {
            $response = $this->getHttpClient()->get(
                $emailsUrl,
                $this->createAuthorizationHeaders($token)
            );
        } catch (\Throwable $e) {
            return '';
        }

        foreach (json_decode($response->getBody(), true) as $email) {
            if ($email['primary'] && $email['verified']) {
                return $email['email'];
            }
        }
    }

    protected function mapUserToObject($user)
    {
        return new User([
            'id' => $user['id'] ?? null,
            'nickname' => $user['login'] ?? null,
            'name' => $user['name'] ?? null,
            'email' => $user['email'] ?? null,
            'avatar' => $user['avatar_url'] ?? null,
        ]);
    }

    
    protected function createAuthorizationHeaders($token)
    {
        return [
            'headers' => [
                'Accept' => 'application/vnd.github.v3+json',
                'Authorization' => sprintf('token %s', $token),
            ],
        ];
    }
}
