<?php



namespace Overtrue\EasySms\Gateways;

use Overtrue\EasySms\Contracts\MessageInterface;
use Overtrue\EasySms\Contracts\PhoneNumberInterface;
use Overtrue\EasySms\Exceptions\GatewayErrorException;
use Overtrue\EasySms\Support\Config;
use Overtrue\EasySms\Traits\HasHttpRequest;


class QiniuGateway extends Gateway
{
    use HasHttpRequest;

    const ENDPOINT_TEMPLATE = 'https://%s.qiniuapi.com/%s/%s';

    const ENDPOINT_VERSION = 'v1';

    
    public function send(PhoneNumberInterface $to, MessageInterface $message, Config $config)
    {
        $endpoint = $this->buildEndpoint('sms', 'message/single');

        $data = $message->getData($this);

        $params = [
            'template_id' => $message->getTemplate($this),
            'mobile' => $to->getNumber(),
        ];

        if (!empty($data)) {
            $params['parameters'] = $data;
        }

        $headers = [
            'Content-Type' => 'application/json',
        ];

        $headers['Authorization'] = $this->generateSign($endpoint, 'POST', json_encode($params), $headers['Content-Type'], $config);

        $result = $this->postJson($endpoint, $params, $headers);

        if (isset($result['error'])) {
            throw new GatewayErrorException($result['message'], $result['error'], $result);
        }

        return $result;
    }

    
    protected function buildEndpoint($type, $function)
    {
        return sprintf(self::ENDPOINT_TEMPLATE, $type, self::ENDPOINT_VERSION, $function);
    }

    
    protected function generateSign($url, $method, $body, $contentType, Config $config)
    {
        $urlItems = parse_url($url);
        $host = $urlItems['host'];
        if (isset($urlItems['port'])) {
            $port = $urlItems['port'];
        } else {
            $port = '';
        }
        $path = $urlItems['path'];
        if (isset($urlItems['query'])) {
            $query = $urlItems['query'];
        } else {
            $query = '';
        }
                $toSignStr = $method.' '.$path;
        if (!empty($query)) {
            $toSignStr .= '?'.$query;
        }
                $toSignStr .= "\nHost: ".$host;
        if (!empty($port)) {
            $toSignStr .= ':'.$port;
        }
                if (!empty($contentType)) {
            $toSignStr .= "\nContent-Type: ".$contentType;
        }
        $toSignStr .= "\n\n";
                if (!empty($body)) {
            $toSignStr .= $body;
        }

        $hmac = hash_hmac('sha1', $toSignStr, $config->get('secret_key'), true);

        return 'Qiniu '.$config->get('access_key').':'.$this->base64UrlSafeEncode($hmac);
    }

    
    protected function base64UrlSafeEncode($data)
    {
        $find = array('+', '/');
        $replace = array('-', '_');

        return str_replace($find, $replace, base64_encode($data));
    }
}
