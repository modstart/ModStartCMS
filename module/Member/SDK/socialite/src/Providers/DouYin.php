<?php

namespace Overtrue\Socialite\Providers;

use Overtrue\Socialite\Exceptions\AuthorizeFailedException;
use Overtrue\Socialite\Exceptions\InvalidArgumentException;
use Overtrue\Socialite\User;


class DouYin extends Base
{
    const NAME = 'douyin';
    protected $baseUrl = 'https://open.douyin.com';
    protected $scopes = ['user_info'];
    protected $openId;

    protected function getAuthUrl()
    {
        return $this->buildAuthUrlFromBase($this->baseUrl . '/platform/oauth/connect/');
    }

    
    public function getCodeFields()
    {
        return [
            'client_key' => $this->getClientId(),
            'redirect_uri' => $this->redirectUrl,
            'scope' => $this->formatScopes($this->scopes, $this->scopeSeparator),
            'response_type' => 'code',
        ];
    }

    protected function getTokenUrl()
    {
        return $this->baseUrl . '/oauth/access_token/';
    }

    
    public function tokenFromCode($code)
    {
        $response = $this->getHttpClient()->get(
            $this->getTokenUrl(),
            [
                'query' => $this->getTokenFields($code),
            ]
        );

        $response = \json_decode($response->getBody()->getContents(), true) ?? [];

        if (empty($response['data'])) {
            throw new AuthorizeFailedException('Invalid token response', $response);
        }

        $this->withOpenId($response['data']['openid']);

        return $this->normalizeAccessTokenResponse($response['data']);
    }

    
    protected function getTokenFields($code)
    {
        return [
            'client_key' => $this->getClientId(),
            'client_secret' => $this->getClientSecret(),
            'code' => $code,
            'grant_type' => 'authorization_code',
        ];
    }

    
    protected function getUserByToken($token)
    {
        $userUrl = $this->baseUrl . '/oauth/userinfo/';

        if (empty($this->openId)) {
            throw new InvalidArgumentException('please set open_id before your query.');
        }

        $response = $this->getHttpClient()->get(
            $userUrl,
            [
                'query' => [
                    'access_token' => $token,
                    'open_id' => $this->openId,
                ],
            ]
        );

        return \json_decode($response->getBody(), true) ?? [];
    }

    
    protected function mapUserToObject($user)
    {
        return new User(
            [
                'id' => $user['open_id'] ?? null,
                'name' => $user['nickname'] ?? null,
                'nickname' => $user['nickname'] ?? null,
                'avatar' => $user['avatar'] ?? null,
                'email' => $user['email'] ?? null,
            ]
        );
    }

    public function withOpenId($openId)
    {
        $this->openId = $openId;

        return $this;
    }
}
