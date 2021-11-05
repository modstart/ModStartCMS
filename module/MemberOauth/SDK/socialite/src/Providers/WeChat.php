<?php

namespace Overtrue\Socialite\Providers;

use Overtrue\Socialite\Exceptions\InvalidArgumentException;
use Overtrue\Socialite\User;
use Psr\Http\Message\ResponseInterface;


class WeChat extends Base
{
    const NAME = 'wechat';
    protected $baseUrl = 'https://api.weixin.qq.com/sns';
    protected $scopes = ['snsapi_login'];
    protected $withCountryCode = false;
    protected $component = null;
    protected $openid = null;

    public function __construct($config)
    {
        parent::__construct($config);
    }

    
    public function withOpenid($openid)
    {
        $this->openid = $openid;

        return $this;
    }

    public function withCountryCode()
    {
        $this->withCountryCode = true;

        return $this;
    }

    
    public function tokenFromCode($code)
    {
        $response = $this->getTokenFromCode($code);

        return $this->normalizeAccessTokenResponse($response->getBody()->getContents());
    }

    
    public function withComponent($componentConfig)
    {
        $this->component = $componentConfig;

        return $this;
    }

    public function getComponent()
    {
        return $this->component;
    }

    protected function getAuthUrl()
    {
        $path = 'oauth2/authorize';

        if (in_array('snsapi_login', $this->scopes)) {
            $path = 'qrconnect';
        }

        return $this->buildAuthUrlFromBase("https://open.weixin.qq.com/connect/{$path}");
    }

    
    protected function buildAuthUrlFromBase($url)
    {
        $query = http_build_query($this->getCodeFields(), '', '&', $this->encodingType);

        return $url . '?' . $query . '#wechat_redirect';
    }

    protected function getCodeFields()
    {
        if (!empty($this->component)) {
            $this->prepareForComponent();
            $this->with(array_merge($this->parameters, ['component_appid' => $this->component['id']]));
        }

        return array_merge([
            'appid' => $this->getClientId(),
            'redirect_uri' => $this->redirectUrl,
            'response_type' => 'code',
            'scope' => $this->formatScopes($this->scopes, $this->scopeSeparator),
            'state' => $this->state ? $this->state : md5(uniqid()),
            'connect_redirect' => 1,
        ], $this->parameters);
    }

    protected function getTokenUrl()
    {
        if (!empty($this->component)) {
            return $this->baseUrl . '/oauth2/component/access_token';
        }

        return $this->baseUrl . '/oauth2/access_token';
    }

    
    public function userFromCode($code)
    {
        if (in_array('snsapi_base', $this->scopes)) {
            $d = \json_decode($this->getTokenFromCode($code)->getBody()->getContents(), true);
            if(empty($d)){
                $d = [];
            }
            return $this->mapUserToObject( $d );
        }

        $token = $this->tokenFromCode($code);

        $this->withOpenid($token['openid']);

        $user = $this->userFromToken($token[$this->accessTokenKey]);

        return $user->setRefreshToken($token['refresh_token'])
            ->setExpiresIn($token['expires_in']);
    }

    
    protected function getUserByToken($token)
    {
        $language = $this->withCountryCode ? null : (isset($this->parameters['lang']) ? $this->parameters['lang'] : 'zh_CN');

        $response = $this->getHttpClient()->get($this->baseUrl . '/userinfo', [
            'query' => array_filter([
                'access_token' => $token,
                'openid' => $this->openid,
                'lang' => $language,
            ]),
        ]);

        $d = @\json_decode($response->getBody()->getContents(), true);
        return $d ? $d : [];
    }

    
    protected function mapUserToObject($user)
    {
        return new User([
            'id' => !empty($user['openid']) ? $user['openid'] :null,
            'name' => !empty($user['nickname']) ? $user['nickname'] : null,
            'nickname' => !empty($user['nickname']) ? $user['nickname'] : null,
            'avatar' => !empty($user['headimgurl']) ? $user['headimgurl'] : null,
            'email' => null,
        ]);
    }

    
    protected function getTokenFields($code)
    {
        if (!empty($this->component)) {
            return [
                'appid' => $this->getClientId(),
                'component_appid' => $this->component['id'],
                'component_access_token' => $this->component['token'],
                'code' => $code,
                'grant_type' => 'authorization_code',
            ];
        }

        return [
            'appid' => $this->getClientId(),
            'secret' => $this->getClientSecret(),
            'code' => $code,
            'grant_type' => 'authorization_code',
        ];
    }

    
    protected function getTokenFromCode($code)
    {
        return $this->getHttpClient()->get($this->getTokenUrl(), [
            'headers' => ['Accept' => 'application/json'],
            'query' => $this->getTokenFields($code),
        ]);
    }

    protected function prepareForComponent()
    {
        if (!$this->getConfig()->has('component')) {
            return;
        }

        $config = [];
        $component = $this->getConfig()->get('component');

        foreach ($component as $key => $value) {
            if (\is_callable($value)) {
                $value = \call_user_func($value, $this);
            }

            switch ($key) {
                case 'id':
                case 'app_id':
                case 'component_app_id':
                    $config['id'] = $value;
                    break;
                case 'token':
                case 'app_token':
                case 'access_token':
                case 'component_access_token':
                    $config['token'] = $value;
                    break;
            }
        }

        if (2 !== count($config)) {
            throw new InvalidArgumentException('Please check your config arguments is available.');
        }

        $this->scopes = ['snsapi_base'];
        $this->component = $config;
    }
}
