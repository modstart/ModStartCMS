<?php

namespace Overtrue\Socialite\Providers;

use Overtrue\Socialite\Contracts\ProviderInterface;
use Overtrue\Socialite\Exceptions\AuthorizeFailedException;
use Overtrue\Socialite\User;

class QCloud extends Base implements ProviderInterface
{
    const NAME = 'qcloud';
    protected $scopes = ['login'];
    protected $accessTokenKey = 'UserAccessToken';
    protected $refreshTokenKey = 'UserRefreshToken';
    protected $expiresInKey = 'ExpiresAt';
    protected $openId;
    protected $unionId;

    protected function getAuthUrl()
    {
        return $this->buildAuthUrlFromBase('https://cloud.tencent.com/open/authorize');
    }

    protected function getTokenUrl()
    {
        return '';
    }

    protected function getAppId()
    {
        return $this->config->get('app_id') ?$this->config->get('app_id'): $this->getClientId();
    }

    protected function getSecretId()
    {
        return $this->config->get('secret_id');
    }

    protected function getSecretKey()
    {
        return $this->config->get('secret_key');
    }

    
    public function TokenFromCode($code)
    {
        $response = $this->performRequest(
            'GET',
            'open.tencentcloudapi.com',
            'GetUserAccessToken',
            '2018-12-25',
            [
                'query' => [
                    'UserAuthCode' => $code,
                ],
            ]
        );

        return $this->parseAccessToken($response);
    }

    
    protected function getUserByToken($token)
    {
        $secret = $this->getFederationToken($token);

        return $this->performRequest(
            'GET',
            'open.tencentcloudapi.com',
            'GetUserBaseInfo',
            '2018-12-25',
            [
                'headers' => [
                    'X-TC-Token' => $secret['Token'],
                ],
            ],
            $secret['TmpSecretId'],
            $secret['TmpSecretKey']
        );
    }

    
    protected function mapUserToObject($user)
    {
        return new User(
            [
                'id' => $this->openId ?$this->openId: null,
                'name' => $user['Nickname'] ?$user['Nickname']: null,
                'nickname' => $user['Nickname'] ?$user['Nickname']: null,
            ]
        );
    }

    public function performRequest($method, $host, $action, $version, $options = [], $secretId = null, $secretKey = null)
    {
        $method = \strtoupper($method);
        $timestamp = \time();
        $credential = \sprintf('%s/%s/tc3_request', \gmdate('Y-m-d', $timestamp), $this->getServiceFromHost($host));
        $options['headers'] = \array_merge(
            $options['headers'] ?$options['headers']: [],
            [
                'X-TC-Action' => $action,
                'X-TC-Timestamp' => $timestamp,
                'X-TC-Version' => $version,
                'Content-Type' => 'application/x-www-form-urlencoded; charset=utf-8',
            ]
        );

        $signature = $this->sign($method, $host, $options['query'] ?$options['query']: [], '', $options['headers'], $credential, $secretKey);
        $options['headers']['Authorization'] =
            \sprintf(
                'TC3-HMAC-SHA256 Credential=%s/%s, SignedHeaders=content-type;host, Signature=%s',
                $secretId ?$secretId: $this->getSecretId(),
                $credential,
                $signature
            );
        $options['debug'] = \fopen(storage_path('logs/laravel-2020-07-15.log'), 'w+');
        $response = $this->getHttpClient()->get("https://{$host}/", $options);

        $response = json_decode($response->getBody()->getContents(), true);
        if(empty($response)){
            $response = [];
        }

        if (!empty($response['Response']['Error'])) {
            throw new AuthorizeFailedException(
                \sprintf('%s: %s', $response['Response']['Error']['Code'], $response['Response']['Error']['Message']),
                $response
            );
        }

        return $response['Response'] ?$response['Response']: [];
    }

    protected function sign($requestMethod, $host, $query, $payload, $headers, $credential, $secretKey = null)
    {
        $canonicalRequestString = \join(
            "\n",
            [
                $requestMethod,
                '/',
                \http_build_query($query),
                "content-type:{$headers['Content-Type']}\nhost:{$host}\n",
                "content-type;host",
                hash('SHA256', $payload),
            ]
        );

        $signString = \join(
            "\n",
            [
                'TC3-HMAC-SHA256',
                $headers['X-TC-Timestamp'],
                $credential,
                hash('SHA256', $canonicalRequestString),
            ]
        );

        $secretKey = $secretKey ?$secretKey: $this->getSecretKey();
        $secretDate = hash_hmac('SHA256', \gmdate('Y-m-d', $headers['X-TC-Timestamp']), "TC3{$secretKey}", true);
        $secretService = hash_hmac('SHA256', $this->getServiceFromHost($host), $secretDate, true);
        $secretSigning = hash_hmac('SHA256', "tc3_request", $secretService, true);

        return hash_hmac('SHA256', $signString, $secretSigning);
    }

    
    protected function parseAccessToken($body)
    {
        if (!is_array($body)) {
            $body = json_decode($body, true);
        }

        if (empty($body['UserOpenId'])) {
            throw new AuthorizeFailedException('Authorize Failed: ' . json_encode($body, JSON_UNESCAPED_UNICODE), $body);
        }

        $this->openId = $body['UserOpenId'] ?$body['UserOpenId']: null;
        $this->unionId = $body['UserUnionId'] ?$body['UserUnionId']: null;

        return $body;
    }

    
    protected function getFederationToken($accessToken)
    {
        $response = $this->performRequest(
            'GET',
            'sts.tencentcloudapi.com',
            'GetThirdPartyFederationToken',
            '2018-08-13',
            [
                'query' => [
                    'UserAccessToken' => $accessToken,
                    'Duration' => 7200,
                    'ApiAppId' => 0,
                ],
                'headers' => [
                    'X-TC-Region' => 'ap-guangzhou',                 ]
            ]
        );

        if (empty($response['Credentials'])) {
            throw new AuthorizeFailedException('Get Federation Token failed.', $response);
        }

        return $response['Credentials'];
    }

    protected function getCodeFields()
    {
        $fields = array_merge(
            [
                'app_id' => $this->getAppId(),
                'redirect_url' => $this->redirectUrl,
                'scope' => $this->formatScopes($this->scopes, $this->scopeSeparator),
                'response_type' => 'code',
            ],
            $this->parameters
        );

        if ($this->state) {
            $fields['state'] = $this->state;
        }

        return $fields;
    }

    
    protected function getServiceFromHost($host)
    {
        return explode('.', $host)[0];
    }
}
