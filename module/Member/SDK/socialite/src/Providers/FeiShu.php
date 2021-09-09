<?php

namespace Overtrue\Socialite\Providers;

use GuzzleHttp\Exception\GuzzleException;
use Overtrue\Socialite\Exceptions\AuthorizeFailedException;
use Overtrue\Socialite\Exceptions\BadRequestException;
use Overtrue\Socialite\Exceptions\Feishu\InvalidTicketException;
use Overtrue\Socialite\Exceptions\InvalidTokenException;
use Overtrue\Socialite\User;


class FeiShu extends Base
{
    const NAME = 'feishu';
    protected $baseUrl       = 'https://open.feishu.cn/open-apis';
    protected $expiresInKey  = 'refresh_expires_in';
    protected   $isInternalApp = false;

    public function __construct($config)
    {
        parent::__construct($config);
        $this->isInternalApp = ($this->config->get('app_mode') ?? $this->config->get('mode')) == 'internal';
    }

    protected function getAuthUrl()
    {
        return $this->buildAuthUrlFromBase($this->baseUrl . '/authen/v1/index');
    }

    protected function getCodeFields()
    {
        return [
            'redirect_uri' => $this->redirectUrl,
            'app_id'       => $this->getClientId(),
        ];
    }

    protected function getTokenUrl()
    {
        return $this->baseUrl . '/authen/v1/access_token';
    }

    
    public function tokenFromCode($code)
    {
        return $this->normalizeAccessTokenResponse($this->getTokenFromCode($code));
    }

    
    protected function getTokenFromCode($code)
    {
        $this->configAppAccessToken();
        $response = $this->getHttpClient()->post(
            $this->getTokenUrl(),
            [
                'json' => [
                    'app_access_token' => $this->config->get('app_access_token'),
                    'code'             => $code,
                    'grant_type'       => 'authorization_code',
                ],
            ]
        );
        $response = \json_decode($response->getBody(), true) ?? [];

        if (empty($response['data'])) {
            throw new AuthorizeFailedException('Invalid token response', $response);
        }

        return $this->normalizeAccessTokenResponse($response['data']);
    }

    
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get(
            $this->baseUrl . '/authen/v1/user_info',
            [
                'headers' => ['Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $token],
                'query'   => array_filter(
                    [
                        'user_access_token' => $token,
                    ]
                ),
            ]
        );

        $response = \json_decode($response->getBody(), true) ?? [];

        if (empty($response['data'])) {
            throw new \InvalidArgumentException('You have error! ' . json_encode($response, JSON_UNESCAPED_UNICODE));
        }

        return $response['data'];
    }

    
    protected function mapUserToObject($user)
    {
        return new User(
            [
                'id'       => $user['user_id'] ?$user['user_id']: null,
                'name'     => $user['name'] ?? null,
                'nickname' => $user['name'] ?? null,
                'avatar'   => $user['avatar_url'] ?? null,
                'email'    => $user['email'] ?? null,
            ]
        );
    }

    public function withInternalAppMode()
    {
        $this->isInternalApp = true;
        return $this;
    }

    public function withDefaultMode()
    {
        $this->isInternalApp = false;
        return $this;
    }

    
    public function withAppTicket($appTicket)
    {
        $this->config->set('app_ticket', $appTicket);
        return $this;
    }

    
    protected function configAppAccessToken()
    {
        $url = $this->baseUrl . '/auth/v3/app_access_token/';
        $params = [
            'json' => [
                'app_id'     => $this->config->get('client_id'),
                'app_secret' => $this->config->get('client_secret'),
                'app_ticket' => $this->config->get('app_ticket'),
            ],
        ];

        if ($this->isInternalApp) {
            $url = $this->baseUrl . '/auth/v3/app_access_token/internal/';
            $params = [
                'json' => [
                    'app_id'     => $this->config->get('client_id'),
                    'app_secret' => $this->config->get('client_secret'),
                ],
            ];
        }

        if (!$this->isInternalApp && !$this->config->has('app_ticket')) {
            throw new InvalidTicketException('You are using default mode, please config \'app_ticket\' first');
        }

        $response = $this->getHttpClient()->post($url, $params);
        $response = \json_decode($response->getBody(), true) ?? [];

        if (empty($response['app_access_token'])) {
            throw new InvalidTokenException('Invalid \'app_access_token\' response', json_encode($response));
        }

        $this->config->set('app_access_token', $response['app_access_token']);
    }

    
    protected function configTenantAccessToken()
    {
        $url = $this->baseUrl . '/auth/v3/tenant_access_token/';
        $params = [
            'json' => [
                'app_id'     => $this->config->get('client_id'),
                'app_secret' => $this->config->get('client_secret'),
                'app_ticket' => $this->config->get('app_ticket'),
            ],
        ];

        if ($this->isInternalApp) {
            $url = $this->baseUrl . '/auth/v3/tenant_access_token/internal/';
            $params = [
                'json' => [
                    'app_id'     => $this->config->get('client_id'),
                    'app_secret' => $this->config->get('client_secret'),
                ],
            ];
        }

        if (!$this->isInternalApp && !$this->config->has('app_ticket')) {
            throw new BadRequestException('You are using default mode, please config \'app_ticket\' first');
        }

        $response = $this->getHttpClient()->post($url, $params);
        $response = \json_decode($response->getBody(), true) ?? [];
        if (empty($response['tenant_access_token'])) {
            throw new AuthorizeFailedException('Invalid tenant_access_token response', $response);
        }

        $this->config->set('tenant_access_token', $response['tenant_access_token']);
    }
}
