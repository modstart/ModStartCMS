<?php

namespace Overtrue\Socialite\Providers;

use Overtrue\Socialite\User;


class Baidu extends Base
{
    const NAME = 'baidu';
    protected $baseUrl = 'https://openapi.baidu.com';
    protected $version = '2.0';
    protected $scopes = ['basic'];
    protected $display = 'popup';

    
    public function withDisplay($display)
    {
        $this->display = $display;

        return $this;
    }

    
    public function withScopes($scopes)
    {
        $this->scopes = $scopes;

        return $this;
    }

    
    protected function getAuthUrl()
    {
        return $this->buildAuthUrlFromBase($this->baseUrl . '/oauth/' . $this->version . '/authorize');
    }

    protected function getCodeFields()
    {
        return [
                'response_type' => 'code',
                'client_id' => $this->getClientId(),
                'redirect_uri' => $this->redirectUrl,
                'scope' => $this->formatScopes($this->scopes, $this->scopeSeparator),
                'display' => $this->display,
            ] + $this->parameters;
    }

    
    protected function getTokenUrl()
    {
        return $this->baseUrl . '/oauth/' . $this->version . '/token';
    }

    
    protected function getTokenFields($code)
    {
        return parent::getTokenFields($code) + ['grant_type' => 'authorization_code'];
    }

    
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get(
            $this->baseUrl . '/rest/' . $this->version . '/passport/users/getInfo',
            [
                'query' => [
                    'access_token' => $token,
                ],
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]
        );

        return json_decode($response->getBody(), true) ?? [];
    }

    
    protected function mapUserToObject($user)
    {
        return new User(
            [
                'id' => $user['userid'] ?? null,
                'nickname' => $user['realname'] ?? null,
                'name' => $user['username'] ?? null,
                'email' => '',
                'avatar' => $user['portrait'] ? 'http://tb.himg.baidu.com/sys/portraitn/item/' . $user['portrait'] : null,
            ]
        );
    }
}
