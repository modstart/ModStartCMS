<?php



namespace Overtrue\EasySms\Gateways;

use GuzzleHttp\Exception\ClientException;
use Overtrue\EasySms\Contracts\MessageInterface;
use Overtrue\EasySms\Contracts\PhoneNumberInterface;
use Overtrue\EasySms\Exceptions\GatewayErrorException;
use Overtrue\EasySms\Support\Config;
use Overtrue\EasySms\Traits\HasHttpRequest;


class TiniyoGateway extends Gateway
{
    use HasHttpRequest;

    const ENDPOINT_URL = 'https://api.tiniyo.com/v1/Account/%s/Message';

    const SUCCESS_CODE = '000000';
    
    public function getName()
    {
        return 'tiniyo';
    }

    
    public function send(PhoneNumberInterface $to, MessageInterface $message, Config $config)
    {
        $accountSid = $config->get('account_sid');
        $endpoint = $this->buildEndPoint($accountSid);

        $params = [
            'dst' => $to->getUniversalNumber(),
            'src' => $config->get('from'),
            'text' => $message->getContent($this),
        ];

        $result = $this->request('post', $endpoint, [
            'json' => $params,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json;charset=utf-8',
                'Authorization' => base64_encode($config->get('account_sid').':'.$config->get('token')),
            ],
        ]);

        if (self::SUCCESS_CODE != $result['statusCode']) {
            throw new GatewayErrorException($result['statusCode'], $result['statusCode'], $result);
        }

        return $result;
    }

    
    protected function buildEndPoint($accountSid)
    {
        return sprintf(self::ENDPOINT_URL, $accountSid);
    }
}
