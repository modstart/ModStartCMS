<?php

namespace Overtrue\Socialite\Providers;

use Overtrue\Socialite\User;


class DingTalk extends Base
{
    const NAME = 'dingtalk';
    protected $getUserByCode = 'https://oapi.dingtalk.com/sns/getuserinfo_bycode';
    protected $scopes = ['snsapi_login'];

    protected function getAuthUrl()
    {
        return $this->buildAuthUrlFromBase('https://oapi.dingtalk.com/connect/qrconnect');
    }

    protected function getTokenUrl()
    {
        throw new \InvalidArgumentException('not supported to get access token.');
    }

    
    protected function getUserByToken($token)
    {
        throw new \InvalidArgumentException('Unable to use token get User.');
    }

    
    protected function mapUserToObject($user)
    {
        return new User(
            [
                'name' => $user['nick'] ?? null,
                'nickname' => $user['nick'] ?? null,
                'id' => $user['openid'] ?? null,
                'email' => null,
                'avatar' => null,
            ]
        );
    }

    protected function getCodeFields()
    {
        return array_merge(
            [
                'appid' => $this->getClientId(),
                'response_type' => 'code',
                'scope' => implode($this->scopes),
                'redirect_uri' => $this->redirectUrl,
            ],
            $this->parameters
        );
    }

    public function getClientId()
    {
        return $this->getConfig()->get('app_id') ?? $this->getConfig()->get('appid') ?? $this->getConfig()->get('appId')
            ?? $this->getConfig()->get('client_id');
    }

    public function getClientSecret()
    {
        return $this->getConfig()->get('app_secret') ?? $this->getConfig()->get('appSecret')
            ?? $this->getConfig()->get('client_secret');
    }

    protected function createSignature(int $time)
    {
        return base64_encode(hash_hmac('sha256', $time, $this->getClientSecret(), true));
    }

    
    public function userFromCode($code)
    {
        $time = (int)microtime(true) * 1000;
        $queryParams = [
            'accessKey' => $this->getClientId(),
            'timestamp' => $time,
            'signature' => $this->createSignature($time),
        ];

        $response = $this->getHttpClient()->post(
            $this->getUserByCode . '?' . http_build_query($queryParams),
            [
                'json' => ['tmp_auth_code' => $code],
            ]
        );
        $response = \json_decode($response->getBody()->getContents(), true);

        if (0 != $response['errcode'] ?? 1) {
            throw new \InvalidArgumentException('You get error: ' . json_encode($response, JSON_UNESCAPED_UNICODE));
        }

        return new User(
            [
                'name' => $response['user_info']['nick'],
                'nickname' => $response['user_info']['nick'],
                'id' => $response['user_info']['openid'],
            ]
        );
    }
}
