<?php

namespace Overtrue\Socialite\Providers;

use Overtrue\Socialite\Exceptions\InvalidTokenException;
use Overtrue\Socialite\User;


class Weibo extends Base
{
    const NAME = 'weibo';
    protected $baseUrl = 'https://api.weibo.com';
    protected $scopes = ['email'];

    protected function getAuthUrl()
    {
        return $this->buildAuthUrlFromBase($this->baseUrl.'/oauth2/authorize');
    }

    protected function getTokenUrl()
    {
        return $this->baseUrl.'/2/oauth2/access_token';
    }

    
    protected function getTokenFields($code)
    {
        return array_merge(parent::getTokenFields($code) , ['grant_type' => 'authorization_code']);
    }

    
    protected function getUserByToken($token)
    {
        $payload = $this->getTokenPayload($token);
        $uid = !empty($payload['uid']) ? $payload['uid'] : null;

        if (empty($uid)) {
            throw new InvalidTokenException('Invalid token.', $token);
        }

        $response = $this->getHttpClient()->get($this->baseUrl.'/2/users/show.json', [
            'query' => [
                'uid' => $uid,
                'access_token' => $token,
            ],
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);

        $result = @\json_decode($response->getBody(), true);
        return $result ? $result : [];
    }

    
    protected function getTokenPayload($token)
    {
        $response = $this->getHttpClient()->post($this->baseUrl.'/oauth2/get_token_info', [
            'query' => [
                'access_token' => $token,
            ],
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);

        $response = @\json_decode($response->getBody(), true) ;
        if(empty($response)){
            $response = [];
        }

        if (empty($response['uid'])) {
            throw new InvalidTokenException(\sprintf('Invalid token %s', $token), $token);
        }

        return $response;
    }

    
    protected function mapUserToObject($user)
    {
        return new User([
            'id' => $user['id']? $user['id'] : null,
            'nickname' => $user['screen_name']? $user['screen_name'] : null,
            'name' => $user['name'] ? $user['name'] : null,
            'email' => !empty($user['email']) ? $user['email'] : null,
            'avatar' => $user['avatar_large'] ? $user['avatar_large'] : null,
        ]);
    }
}
