<?php



namespace Overtrue\EasySms\Gateways;

use Overtrue\EasySms\Contracts\MessageInterface;
use Overtrue\EasySms\Contracts\PhoneNumberInterface;
use Overtrue\EasySms\Exceptions\GatewayErrorException;
use Overtrue\EasySms\Exceptions\InvalidArgumentException;
use Overtrue\EasySms\Support\Config;
use Overtrue\EasySms\Traits\HasHttpRequest;


class ChuanglanGateway extends Gateway
{
    use HasHttpRequest;

    
    const ENDPOINT_URL_TEMPLATE = 'https://%s.253.com/msg/send/json';

    
    const INT_URL = 'http://intapi.253.com/send/json';

    
    const CHANNEL_VALIDATE_CODE = 'smsbj1';

    
    const CHANNEL_PROMOTION_CODE = 'smssh1';

    
    public function send(PhoneNumberInterface $to, MessageInterface $message, Config $config)
    {
        $IDDCode = !empty($to->getIDDCode()) ? $to->getIDDCode() : 86;

        $params = [
            'account' => $config->get('account'),
            'password' => $config->get('password'),
            'phone' => $to->getNumber(),
            'msg' => $this->wrapChannelContent($message->getContent($this), $config, $IDDCode),
        ];

        if (86 != $IDDCode) {
            $params['mobile'] = $to->getIDDCode().$to->getNumber();
            $params['account'] = $config->get('intel_account') ?: $config->get('account');
            $params['password'] = $config->get('intel_password') ?: $config->get('password');
        }

        $result = $this->postJson($this->buildEndpoint($config, $IDDCode), $params);

        if (!isset($result['code']) || '0' != $result['code']) {
            throw new GatewayErrorException(json_encode($result, JSON_UNESCAPED_UNICODE), isset($result['code']) ? $result['code'] : 0, $result);
        }

        return $result;
    }

    
    protected function buildEndpoint(Config $config, $IDDCode = 86)
    {
        $channel = $this->getChannel($config, $IDDCode);

        if (self::INT_URL === $channel) {
            return $channel;
        }

        return sprintf(self::ENDPOINT_URL_TEMPLATE, $channel);
    }

    
    protected function getChannel(Config $config, $IDDCode)
    {
        if (86 != $IDDCode) {
            return self::INT_URL;
        }
        $channel = $config->get('channel', self::CHANNEL_VALIDATE_CODE);

        if (!in_array($channel, [self::CHANNEL_VALIDATE_CODE, self::CHANNEL_PROMOTION_CODE])) {
            throw new InvalidArgumentException('Invalid channel for ChuanglanGateway.');
        }

        return $channel;
    }

    
    protected function wrapChannelContent($content, Config $config, $IDDCode)
    {
        $channel = $this->getChannel($config, $IDDCode);

        if (self::CHANNEL_PROMOTION_CODE == $channel) {
            $sign = (string) $config->get('sign', '');
            if (empty($sign)) {
                throw new InvalidArgumentException('Invalid sign for ChuanglanGateway when using promotion channel');
            }

            $unsubscribe = (string) $config->get('unsubscribe', '');
            if (empty($unsubscribe)) {
                throw new InvalidArgumentException('Invalid unsubscribe for ChuanglanGateway when using promotion channel');
            }

            $content = $sign.$content.$unsubscribe;
        }

        return $content;
    }
}
