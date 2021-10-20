<?php



namespace Overtrue\EasySms\Gateways;

use Overtrue\EasySms\Contracts\MessageInterface;
use Overtrue\EasySms\Contracts\PhoneNumberInterface;
use Overtrue\EasySms\Exceptions\GatewayErrorException;
use Overtrue\EasySms\Support\Config;
use Overtrue\EasySms\Traits\HasHttpRequest;


class BaiduGateway extends Gateway
{
    use HasHttpRequest;

    const ENDPOINT_HOST = 'smsv3.bj.baidubce.com';

    const ENDPOINT_URI = '/api/v3/sendSms';

    const BCE_AUTH_VERSION = 'bce-auth-v1';

    const DEFAULT_EXPIRATION_IN_SECONDS = 1800; 
    const SUCCESS_CODE = 1000;

    
    public function send(PhoneNumberInterface $to, MessageInterface $message, Config $config)
    {
        $params = [
            'signatureId' => $config->get('invoke_id'),
            'mobile' => $to->getNumber(),
            'template' => $message->getTemplate($this),
            'contentVar' => $message->getData($this),
        ];
        if (!empty($params['contentVar']['custom'])) {
                        $params['custom'] = $params['contentVar']['custom'];
            unset($params['contentVar']['custom']);
        }
        if (!empty($params['contentVar']['userExtId'])) {
                        $params['userExtId'] = $params['contentVar']['userExtId'];
            unset($params['contentVar']['userExtId']);
        }

        $datetime = gmdate('Y-m-d\TH:i:s\Z');

        $headers = [
            'host' => self::ENDPOINT_HOST,
            'content-type' => 'application/json',
            'x-bce-date' => $datetime,
        ];
                $signHeaders = $this->getHeadersToSign($headers, ['host', 'x-bce-date']);

        $headers['Authorization'] = $this->generateSign($signHeaders, $datetime, $config);

        $result = $this->request('post', self::buildEndpoint($config), ['headers' => $headers, 'json' => $params]);

        if (self::SUCCESS_CODE != $result['code']) {
            throw new GatewayErrorException($result['message'], $result['code'], $result);
        }

        return $result;
    }

    
    protected function buildEndpoint(Config $config)
    {
        return 'http://'.$config->get('domain', self::ENDPOINT_HOST).self::ENDPOINT_URI;
    }

    
    protected function generateSign(array $signHeaders, $datetime, Config $config)
    {
                $authString = self::BCE_AUTH_VERSION.'/'.$config->get('ak').'/'
            .$datetime.'/'.self::DEFAULT_EXPIRATION_IN_SECONDS;

                $signingKey = hash_hmac('sha256', $authString, $config->get('sk'));
                        $canonicalURI = str_replace('%2F', '/', rawurlencode(self::ENDPOINT_URI));

                $canonicalQueryString = ''; 
                $signedHeaders = empty($signHeaders) ? '' : strtolower(trim(implode(';', array_keys($signHeaders))));

                $canonicalHeader = $this->getCanonicalHeaders($signHeaders);

                $canonicalRequest = "POST\n{$canonicalURI}\n{$canonicalQueryString}\n{$canonicalHeader}";

                $signature = hash_hmac('sha256', $canonicalRequest, $signingKey);

                return "{$authString}/{$signedHeaders}/{$signature}";
    }

    
    protected function getCanonicalHeaders(array $headers)
    {
        $headerStrings = [];
        foreach ($headers as $name => $value) {
                        $headerStrings[] = rawurlencode(strtolower(trim($name))).':'.rawurlencode(trim($value));
        }

        sort($headerStrings);

        return implode("\n", $headerStrings);
    }

    
    protected function getHeadersToSign(array $headers, array $keys)
    {
        return array_intersect_key($headers, array_flip($keys));
    }
}
