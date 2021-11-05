<?php

namespace Overtrue\Socialite\Providers;

use Overtrue\Socialite\User;


class Taobao extends Base
{
    const NAME = 'taobao';
    protected $baseUrl = 'https://oauth.taobao.com';
    protected $gatewayUrl = 'https://eco.taobao.com/router/rest';
    protected $view = 'web';
    protected $scopes = ['user_info'];

    public function withView($view)
    {
        $this->view = $view;

        return $this;
    }

    protected function getAuthUrl()
    {
        return $this->buildAuthUrlFromBase($this->baseUrl.'/authorize');
    }

    public function getCodeFields()
    {
        return [
            'client_id' => $this->getClientId(),
            'redirect_uri' => $this->redirectUrl,
            'view' => $this->view,
            'response_type' => 'code',
        ];
    }

    protected function getTokenUrl()
    {
        return $this->baseUrl.'/token';
    }

    
    protected function getTokenFields($code)
    {
        return parent::getTokenFields($code) + ['grant_type' => 'authorization_code', 'view' => $this->view];
    }

    
    public function tokenFromCode($code)
    {
        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
            'query' => $this->getTokenFields($code),
        ]);

        return $this->normalizeAccessTokenResponse($response->getBody()->getContents());
    }

    
    protected function getUserByToken($token, $query = [])
    {
        $response = $this->getHttpClient()->post($this->getUserInfoUrl($this->gatewayUrl, $token));

        return \json_decode($response->getBody()->getContents(), true) ?? [];
    }

    
    protected function mapUserToObject($user)
    {
        return new User([
            'id' => $user['open_id'] ?? null,
            'nickname' => $user['nick'] ?? null,
            'name' => $user['nick'] ?? null,
            'avatar' => $user['avatar'] ?? null,
            'email' => $user['email'] ?? null,
        ]);
    }

    
    protected function generateSign($params)
    {
        ksort($params);

        $stringToBeSigned = $this->getConfig()->get('client_secret');

        foreach ($params as $k => $v) {
            if (!is_array($v) && '@' != substr($v, 0, 1)) {
                $stringToBeSigned .= "$k$v";
            }
        }

        $stringToBeSigned .= $this->getConfig()->get('client_secret');

        return strtoupper(md5($stringToBeSigned));
    }

    
    protected function getPublicFields($token, $apiFields = [])
    {
        $fields = [
            'app_key' => $this->getClientId(),
            'sign_method' => 'md5',
            'session' => $token,
            'timestamp' => \date('Y-m-d H:i:s'),
            'v' => '2.0',
            'format' => 'json',
        ];

        $fields = array_merge($apiFields, $fields);
        $fields['sign'] = $this->generateSign($fields);

        return $fields;
    }

    
    protected function getUserInfoUrl($url, $token)
    {
        $apiFields = ['method' => 'taobao.miniapp.userInfo.get'];

        $query = http_build_query($this->getPublicFields($token, $apiFields), '', '&', $this->encodingType);

        return $url.'?'.$query;
    }
}
