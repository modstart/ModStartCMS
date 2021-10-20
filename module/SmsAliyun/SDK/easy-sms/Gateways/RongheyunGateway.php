<?php



namespace Overtrue\EasySms\Gateways;

use Overtrue\EasySms\Contracts\MessageInterface;
use Overtrue\EasySms\Contracts\PhoneNumberInterface;
use Overtrue\EasySms\Exceptions\GatewayErrorException;
use Overtrue\EasySms\Support\Config;
use Overtrue\EasySms\Traits\HasHttpRequest;


class RongheyunGateway extends Gateway
{
    use HasHttpRequest;

    const ENDPOINT_URL = 'https://api.mix2.zthysms.com/v2/sendSmsTp';

    
    public function send(PhoneNumberInterface $to, MessageInterface $message, Config $config)
    {
        $tKey = time();
        $password = md5(md5($config->get('password')) . $tKey);
        $params = [
            'username' => $config->get('username', ''),
            'password' => $password,
            'tKey' => $tKey,
            'signature' => $config->get('signature', ''),
            'tpId' => $message->getTemplate($this),
            'ext' => '',
            'extend' => '',
            'records' => [
                'mobile' => $to->getNumber(),
                'tpContent' => $message->getData($this),
            ],
        ];

        $result = $this->postJson(
            self::ENDPOINT_URL,
            $params,
            ['Content-Type' => 'application/json; charset="UTF-8"']
        );
        if (200 != $result['code']) {
            throw new GatewayErrorException($result['msg'], $result['code'], $result);
        }

        return $result;
    }
}
