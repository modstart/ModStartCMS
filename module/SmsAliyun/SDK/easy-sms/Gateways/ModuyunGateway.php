<?php



namespace Overtrue\EasySms\Gateways;

use Overtrue\EasySms\Contracts\MessageInterface;
use Overtrue\EasySms\Contracts\PhoneNumberInterface;
use Overtrue\EasySms\Exceptions\GatewayErrorException;
use Overtrue\EasySms\Support\Config;
use Overtrue\EasySms\Traits\HasHttpRequest;


class ModuyunGateway extends Gateway
{
    use HasHttpRequest;

    const ENDPOINT_URL = 'https://live.moduyun.com/sms/v2/sendsinglesms';

    
    public function send(PhoneNumberInterface $to, MessageInterface $message, Config $config)
    {
        $urlParams = [
            'accesskey' => $config->get('accesskey'),
            'random' => rand(100000, 999999),
        ];

        $params = [
            'tel' => [
                'mobile' => $to->getNumber(),
                'nationcode' => $to->getIDDCode() ?: '86',
            ],
            'signId' => $config->get('signId', ''),
            'templateId' => $message->getTemplate($this),
            'time' => time(),
            'type' => $config->get('type', 0),
            'params' => array_values($message->getData($this)),
            'ext' => '',
            'extend' => '',
        ];
        $params['sig'] = $this->generateSign($params, $urlParams['random']);

        $result = $this->postJson($this->getEndpointUrl($urlParams), $params);
        $result = is_string($result) ? json_decode($result, true) : $result;
        if (0 != $result['result']) {
            throw new GatewayErrorException($result['errmsg'], $result['result'], $result);
        }

        return $result;
    }

    
    protected function getEndpointUrl($params)
    {
        return self::ENDPOINT_URL . '?' . http_build_query($params);
    }

    
    protected function generateSign($params, $random)
    {
        return hash('sha256', sprintf(
            'secretkey=%s&random=%d&time=%d&mobile=%s',
            $this->config->get('secretkey'),
            $random,
            $params['time'],
            $params['tel']['mobile']
        ));
    }
}
