<?php

namespace Overtrue\Socialite\Providers;

use GuzzleHttp\Psr7\Stream;
use Overtrue\Socialite\Exceptions\AuthorizeFailedException;
use Overtrue\Socialite\Exceptions\BadRequestException;
use Overtrue\Socialite\User;


class Tapd extends Base
{
    const NAME = 'tapd';
    protected $baseUrl = 'https://api.tapd.cn';

    
    protected function getAuthUrl()
    {
        return $this->buildAuthUrlFromBase($this->baseUrl . '/quickstart/testauth');
    }

    
    protected function getTokenUrl()
    {
        return $this->baseUrl . '/tokens/request_token';
    }

    
    protected function getRefreshTokenUrl()
    {
        return $this->baseUrl . '/tokens/refresh_token';
    }

    
    public function tokenFromCode($code)
    {
        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Basic ' . \base64_encode(\sprintf('%s:%s', $this->getClientId(), $this->getClientSecret()))
            ],
            'form_params' => $this->getTokenFields($code),
        ]);

        return $this->normalizeAccessTokenResponse($response->getBody()->getContents());
    }

    
    protected function getTokenFields($code)
    {
        return [
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->redirectUrl,
            'code' => $code,
        ];
    }

    
    protected function getRefreshTokenFields($refreshToken)
    {
        return [
            'grant_type' => 'refresh_token',
            'redirect_uri' => $this->redirectUrl,
            'refresh_token' => $refreshToken,
        ];
    }

    
    public function tokenFromRefreshToken($refreshToken)
    {
        $response = $this->getHttpClient()->post($this->getRefreshTokenUrl(), [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Basic ' . \base64_encode(\sprintf('%s:%s', $this->getClientId(), $this->getClientSecret()))
            ],
            'form_params' => $this->getRefreshTokenFields($refreshToken),
        ]);

        return $this->normalizeAccessTokenResponse($response->getBody()->getContents());
    }

    
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get($this->baseUrl . '/users/info', [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        return \json_decode($response->getBody(), true) ?? [];
    }

    
    protected function mapUserToObject($user)
    {
        if (!isset($user['status']) && $user['status'] != 1) {
            throw new BadRequestException("用户信息获取失败");
        }

        return new User([
            'id' => $user['data']['id'] ?? null,
            'nickname' => $user['data']['nick'] ?? null,
            'name' => $user['data']['name'] ?? null,
            'email' => $user['data']['email'] ?? null,
            'avatar' => $user['data']['avatar'] ?? null,
        ]);
    }

    
    protected function normalizeAccessTokenResponse($response)
    {
        if ($response instanceof Stream) {
            $response->rewind();
            $response = $response->getContents();
        }

        if (\is_string($response)) {
            $response = json_decode($response, true) ?? [];
        }

        if (!\is_array($response)) {
            throw new AuthorizeFailedException('Invalid token response', [$response]);
        }

        if (empty($response['data'][$this->accessTokenKey])) {
            throw new AuthorizeFailedException('Authorize Failed: ' . json_encode($response, JSON_UNESCAPED_UNICODE), $response);
        }

        return $response + [
                'access_token' => $response['data'][$this->accessTokenKey],
                'refresh_token' => $response['data'][$this->refreshTokenKey] ?? null,
                'expires_in' => \intval($response['data'][$this->expiresInKey] ?? 0),
            ];
    }
}
