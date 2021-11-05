<?php

namespace Overtrue\Socialite\Providers;

use Overtrue\Socialite\Exceptions\AuthorizeFailedException;
use Overtrue\Socialite\Exceptions\InvalidArgumentException;
use Overtrue\Socialite\Exceptions\MethodDoesNotSupportException;
use Overtrue\Socialite\User;

class WeWork extends Base
{
    const NAME = 'wework';
    protected $detailed = false;
    protected ?int $agentId;
    protected $apiAccessToken;

    
    public function setAgentId(int $agentId)
    {
        $this->agentId = $agentId;

        return $this;
    }

    public function userFromCode($code)
    {
        $token = $this->getApiAccessToken();
        $user = $this->getUserId($token, $code);

        if ($this->detailed) {
            $user = $this->getUserById($user['UserId']);
        }

        return $this->mapUserToObject($user)->setProvider($this)->setRaw($user);
    }

    public function detailed()
    {
        $this->detailed = true;

        return $this;
    }

    
    public function withApiAccessToken($apiAccessToken)
    {
        $this->apiAccessToken = $apiAccessToken;

        return $this;
    }

    
    public function getAuthUrl()
    {
                if (!empty($this->scopes)) {
            return $this->getOAuthUrl();
        }

                return $this->getQrConnectUrl();
    }

    protected function getOAuthUrl()
    {
        $queries = [
            'appid' => $this->getClientId(),
            'redirect_uri' => $this->redirectUrl,
            'response_type' => 'code',
            'scope' => $this->formatScopes($this->scopes, $this->scopeSeparator),
            'state' => $this->state,
        ];

        return sprintf('https://open.weixin.qq.com/connect/oauth2/authorize?%s#wechat_redirect', http_build_query($queries));
    }

    public function getQrConnectUrl()
    {
        $queries = [
            'appid' => $this->getClientId(),
            'agentid' => $this->agentId ?? $this->config->get('agentid'),
            'redirect_uri' => $this->redirectUrl,
            'state' => $this->state,
        ];

        if (empty($queries['agentid'])) {
            throw new InvalidArgumentException('You must config the `agentid` in configuration or using `setAgentid($agentId)`.');
        }

        return sprintf('https://open.work.weixin.qq.com/wwopen/sso/qrConnect?%s#wechat_redirect', http_build_query($queries));
    }

    
    protected function getUserByToken($token)
    {
        throw new MethodDoesNotSupportException('WeWork doesn\'t support access_token mode');
    }

    protected function getApiAccessToken()
    {
        return $this->apiAccessToken ?? $this->apiAccessToken = $this->createApiAccessToken();
    }

    
    protected function getUserId($token, $code)
    {
        $response = $this->getHttpClient()->get(
            'https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo',
            [
                'query' => array_filter(
                    [
                        'access_token' => $token,
                        'code' => $code,
                    ]
                ),
            ]
        );

        $response = \json_decode($response->getBody(), true) ?? [];

        if (($response['errcode'] ?? 1) > 0 || (empty($response['UserId']) && empty($response['OpenId']))) {
            throw new AuthorizeFailedException('Failed to get user openid:' . $response['errmsg'] ?? 'Unknown.', $response);
        } else if (empty($response['UserId'])) {
            $this->detailed = false;
        }

        return $response;
    }

    
    protected function getUserById($userId)
    {
        $response = $this->getHttpClient()->post(
            'https://qyapi.weixin.qq.com/cgi-bin/user/get',
            [
                'query' => [
                    'access_token' => $this->getApiAccessToken(),
                    'userid' => $userId,
                ],
            ]
        );

        $response = \json_decode($response->getBody(), true) ?? [];

        if (($response['errcode'] ?? 1) > 0 || empty($response['userid'])) {
            throw new AuthorizeFailedException('Failed to get user:' . $response['errmsg'] ?? 'Unknown.', $response);
        }

        return $response;
    }

    
    protected function mapUserToObject($user)
    {
        if ($this->detailed) {
            return new User(
                [
                    'id' => $user['userid'] ?? null,
                    'name' => $user['name'] ?? null,
                    'avatar' => $user['avatar'] ?? null,
                    'email' => $user['email'] ?? null,
                ]
            );
        }

        return new User(
            [
                'id' => $user['UserId'] ?? null ?: $user['OpenId'] ?? null,
            ]
        );
    }

    
    protected function createApiAccessToken()
    {
        $response = $this->getHttpClient()->get(
            'https://qyapi.weixin.qq.com/cgi-bin/gettoken',
            [
                'query' => array_filter(
                    [
                        'corpid' => $this->config->get('corp_id') ?? $this->config->get('corpid'),
                        'corpsecret' => $this->config->get('corp_secret') ?? $this->config->get('corpsecret'),
                    ]
                ),
            ]
        );

        $response = \json_decode($response->getBody(), true) ?? [];

        if (($response['errcode'] ?? 1) > 0) {
            throw new AuthorizeFailedException('Failed to get api access_token:' . $response['errmsg'] ?? 'Unknown.', $response);
        }

        return $response['access_token'];
    }

    protected function getTokenUrl()
    {
        return '';
    }
}
