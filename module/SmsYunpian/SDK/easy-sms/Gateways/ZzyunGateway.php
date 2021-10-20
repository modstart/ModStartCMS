<?php



namespace Overtrue\EasySms\Gateways;

use Overtrue\EasySms\Contracts\MessageInterface;
use Overtrue\EasySms\Contracts\PhoneNumberInterface;
use Overtrue\EasySms\Exceptions\GatewayErrorException;
use Overtrue\EasySms\Support\Config;
use Overtrue\EasySms\Traits\HasHttpRequest;


class ZzyunGateway extends Gateway
{
    use HasHttpRequest;

    const ENDPOINT_URL = 'https://zzyun.com/api/sms/sendByTplCode';

    
    public function send(PhoneNumberInterface $to, MessageInterface $message, Config $config)
    {
        $time = time();
        $user_id = $config->get('user_id');
        $token = md5($time . $user_id . $config->get('secret'));
        $params = [
            'user_id' => $user_id,
            'time' => $time,
            'token' => $token,
            'mobiles' => $to->getNumber(),            'tpl_code' => $message->getTemplate($this),
            'tpl_params' => $message->getData($this),
            'sign_name' => $config->get('sign_name'),
        ];

        $result = $this->post(self::ENDPOINT_URL, $params);

        if ('Success' != $result['Code']) {
            throw new GatewayErrorException($result['Message'], $result['Code'], $result);
        }

        return $result;
    }
}
