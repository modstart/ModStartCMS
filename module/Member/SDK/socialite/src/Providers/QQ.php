<?php

namespace Overtrue\Socialite\Providers;

use Overtrue\Socialite\User;


class QQ extends Base
{
    const NAME = 'qq';
    protected $baseUrl = 'https://graph.qq.com';
    protected $scopes = ['get_user_info'];
    protected $withUnionId = false;

    protected function getAuthUrl()
    {
        return $this->buildAuthUrlFromBase($this->baseUrl.'/oauth2.0/authorize');
    }

    protected function getTokenUrl()
    {
        return $this->baseUrl.'/oauth2.0/token';
    }

    
    protected function getTokenFields($code)
    {
        return array_merge(parent::getTokenFields($code) , [
            'grant_type' => 'authorization_code',
        ]);
    }

    
    public function tokenFromCode($code)
    {
        $response = $this->getHttpClient()->get($this->getTokenUrl(), [
            'query' => $this->getTokenFields($code),
        ]);

        \parse_str($response->getBody()->getContents(), $token);

        return $this->normalizeAccessTokenResponse($token);
    }

    public function withUnionId()
    {
        $this->withUnionId = true;

        return $this;
    }

    
    protected function getUserByToken($token)
    {
        $url = $this->baseUrl.'/oauth2.0/me?fmt=json&access_token='.$token;
        $this->withUnionId && $url .= '&unionid=1';

        $response = $this->getHttpClient()->get($url);

        $me = \json_decode($response->getBody()->getContents(), true);

        $queries = [
            'access_token' => $token,
            'fmt' => 'json',
            'openid' => $me['openid'],
            'oauth_consumer_key' => $this->getClientId(),
        ];

        $response = $this->getHttpClient()->get($this->baseUrl.'/user/get_user_info?'.http_build_query($queries));

        $d = \json_decode($response->getBody()->getContents(), true);
        return array_merge(( $d?$d: []) , [
            'unionid' => !empty($me['unionid']) ?$me['unionid']: null,
            'openid' => $me['openid'] ?$me['openid']: null,
        ]);
    }

    
    protected function mapUserToObject($user)
    {
        return new User([
            'id' => $user['openid'] ?$user['openid']: null,
            'name' => $user['nickname'] ?$user['nickname']: null,
            'nickname' => $user['nickname'] ?$user['nickname']: null,
            'email' => !empty($user['email']) ?$user['email']: null,
            'avatar' => $user['figureurl_qq_2'] ?$user['figureurl_qq_2']: null,
        ]);
    }
}
