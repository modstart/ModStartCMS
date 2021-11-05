<?php

namespace Overtrue\Socialite\Providers;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Stream;
use Overtrue\Socialite\Config;
use Overtrue\Socialite\Contracts\ProviderInterface;
use Overtrue\Socialite\Exceptions\AuthorizeFailedException;
use Overtrue\Socialite\Exceptions\MethodDoesNotSupportException;
use Overtrue\Socialite\User;

abstract class Base implements ProviderInterface
{
    const NAME = null;

    protected      $state           = null;
    protected       $config;
    protected      $redirectUrl;
    protected        $parameters      = [];
    protected        $scopes          = [];
    protected       $scopeSeparator  = ',';
    protected  $httpClient;
    protected        $guzzleOptions   = [];
    protected           $encodingType    = PHP_QUERY_RFC1738;
    protected       $expiresInKey    = 'expires_in';
    protected       $accessTokenKey  = 'access_token';
    protected       $refreshTokenKey = 'refresh_token';

    public function __construct($config)
    {
        $this->config = new Config($config);
        $this->scopes = !empty($config['scopes']) ?$config['scopes']:( $this->scopes ? $this->scopes: []);

                if (!$this->config->has('client_id')) {
            $id = $this->config->get('app_id');
            if (null != $id) {
                $this->config->set('client_id', $id);
            }
        }

                if (!$this->config->has('client_secret')) {
            $secret = $this->config->get('app_secret');
            if (null != $secret) {
                $this->config->set('client_secret', $secret);
            }
        }

                if (!$this->config->has('redirect_url')) {
            $this->config->set('redirect_url', $this->config->get('redirect'));
        }
        $this->redirectUrl = $this->config->get('redirect_url');
    }

    abstract protected function getAuthUrl();

    abstract protected function getTokenUrl();

    abstract protected function getUserByToken($token);

    abstract protected function mapUserToObject($user);

    
    public function redirect($redirectUrl = null)
    {
        if (!empty($redirectUrl)) {
            $this->withRedirectUrl($redirectUrl);
        }

        return $this->getAuthUrl();
    }

    
    public function userFromCode($code)
    {
        $tokenResponse = $this->tokenFromCode($code);
        $user = $this->userFromToken($tokenResponse[$this->accessTokenKey]);

        return $user->setRefreshToken($tokenResponse[$this->refreshTokenKey] ?$tokenResponse[$this->refreshTokenKey]: null)
            ->setExpiresIn($tokenResponse[$this->expiresInKey] ?$tokenResponse[$this->expiresInKey]: null)
            ->setTokenResponse($tokenResponse);
    }

    
    public function userFromToken($token)
    {
        $user = $this->getUserByToken($token);

        return $this->mapUserToObject($user)->setProvider($this)->setRaw($user)->setAccessToken($token);
    }

    
    public function tokenFromCode($code)
    {
        $response = $this->getHttpClient()->post(
            $this->getTokenUrl(),
            [
                'form_params' => $this->getTokenFields($code),
                'headers'     => [
                    'Accept' => 'application/json',
                ],
            ]
        );
        return $this->normalizeAccessTokenResponse($response->getBody()->getContents());
    }

    
    public function refreshToken($refreshToken)
    {
        throw new MethodDoesNotSupportException('refreshToken does not support.');
    }

    
    public function withRedirectUrl($redirectUrl)
    {
        $this->redirectUrl = $redirectUrl;

        return $this;
    }

    
    public function withState($state)
    {
        $this->state = $state;

        return $this;
    }

    
    public function scopes($scopes)
    {
        $this->scopes = $scopes;

        return $this;
    }

    
    public function with($parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    public function getConfig()
    {
        return $this->config;
    }

    
    public function withScopeSeparator($scopeSeparator)
    {
        $this->scopeSeparator = $scopeSeparator;

        return $this;
    }

    public function getClientId()
    {
        return $this->config->get('client_id');
    }

    public function getClientSecret()
    {
        return $this->config->get('client_secret');
    }

    public function getHttpClient()
    {
        return $this->httpClient ?$this->httpClient: new GuzzleClient($this->guzzleOptions);
    }

    
    public function setGuzzleOptions($config = [])
    {
        $this->guzzleOptions = $config;

        return $this;
    }

    public function getGuzzleOptions()
    {
        return $this->guzzleOptions;
    }

    
    protected function formatScopes($scopes, $scopeSeparator)
    {
        return implode($scopeSeparator, $scopes);
    }

    
    protected function getTokenFields($code)
    {
        return [
            'client_id'     => $this->getClientId(),
            'client_secret' => $this->getClientSecret(),
            'code'          => $code,
            'redirect_uri'  => $this->redirectUrl,
        ];
    }

    
    protected function buildAuthUrlFromBase($url)
    {
        $query = array_merge($this->getCodeFields() , ($this->state ? ['state' => $this->state] : []));

        return $url . '?' . \http_build_query($query, '', '&', $this->encodingType);
    }

    protected function getCodeFields()
    {
        $fields = array_merge(
            [
                'client_id'     => $this->getClientId(),
                'redirect_uri'  => $this->redirectUrl,
                'scope'         => $this->formatScopes($this->scopes, $this->scopeSeparator),
                'response_type' => 'code',
            ],
            $this->parameters
        );

        if ($this->state) {
            $fields['state'] = $this->state;
        }

        return $fields;
    }

    
    protected function normalizeAccessTokenResponse($response)
    {
        if ($response instanceof Stream) {
            $response->rewind();
            $response = $response->getContents();
        }

        if (\is_string($response)) {
            $response = json_decode($response, true);
            $response = $response?$response: [];
        }

        if (!\is_array($response)) {
            throw new AuthorizeFailedException('Invalid token response', [$response]);
        }

        if (empty($response[$this->accessTokenKey])) {
            throw new AuthorizeFailedException('Authorize Failed: ' . json_encode($response, JSON_UNESCAPED_UNICODE), $response);
        }

        $d = \intval($response[$this->expiresInKey]);
        return array_merge($response , [
                'access_token'  => $response[$this->accessTokenKey],
                'refresh_token' => !empty($response[$this->refreshTokenKey]) ?$response[$this->refreshTokenKey]: null,
                'expires_in'    => $d ? $d: 0,
            ]);
    }
}
