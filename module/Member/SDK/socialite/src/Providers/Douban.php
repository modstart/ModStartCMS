<?php

namespace Overtrue\Socialite\Providers;

use Overtrue\Socialite\User;


class Douban extends Base
{
    const NAME = 'douban';

    protected function getAuthUrl()
    {
        return $this->buildAuthUrlFromBase('https://www.douban.com/service/auth2/auth');
    }

    protected function getTokenUrl()
    {
        return 'https://www.douban.com/service/auth2/token';
    }

    
    protected function getUserByToken($token, $query = [])
    {
        $response = $this->getHttpClient()->get('https://api.douban.com/v2/user/~me', [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true) ?? [];
    }

    
    protected function mapUserToObject($user)
    {
        return new User([
            'id' => $user['id'] ?? null,
            'nickname' => $user['name'] ?? null,
            'name' => $user['name'] ?? null,
            'avatar' => $user['avatar'] ?? null,
            'email' => null,
        ]);
    }

    
    protected function getTokenFields($code)
    {
        return parent::getTokenFields($code) + ['grant_type' => 'authorization_code'];
    }

    
    public function tokenFromCode($code)
    {
        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
            'form_params' => $this->getTokenFields($code),
        ]);

        return $this->normalizeAccessTokenResponse($response->getBody()->getContents());
    }
}
