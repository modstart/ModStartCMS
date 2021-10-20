<?php



namespace Overtrue\EasySms\Gateways;

use Overtrue\EasySms\Contracts\MessageInterface;
use Overtrue\EasySms\Contracts\PhoneNumberInterface;
use Overtrue\EasySms\Exceptions\GatewayErrorException;
use Overtrue\EasySms\Support\Config;
use Overtrue\EasySms\Traits\HasHttpRequest;


class QcloudGateway extends Gateway
{
    use HasHttpRequest;

    const ENDPOINT_URL = 'https://yun.tim.qq.com/v5/';

    const ENDPOINT_METHOD = 'tlssmssvr/sendsms';

    const ENDPOINT_VERSION = 'v5';

    const ENDPOINT_FORMAT = 'json';

    
    public function send(PhoneNumberInterface $to, MessageInterface $message, Config $config)
    {
        $data = $message->getData($this);

        $signName = !empty($data['sign_name']) ? $data['sign_name'] : $config->get('sign_name', '');

        unset($data['sign_name']);

        $msg = $message->getContent($this);
        if (!empty($msg) && '【' != mb_substr($msg, 0, 1) && !empty($signName)) {
            $msg = '【'.$signName.'】'.$msg;
        }

        $type = !empty($data['type']) ? $data['type'] : 0;
        $params = [
            'tel' => [
                'nationcode' => $to->getIDDCode() ?: 86,
                'mobile' => $to->getNumber(),
            ],
            'type' => $type,
            'msg' => $msg,
            'time' => time(),
            'extend' => '',
            'ext' => '',
        ];
        if (!is_null($message->getTemplate($this)) && is_array($data)) {
            unset($params['msg']);
            $params['params'] = array_values($data);
            $params['tpl_id'] = $message->getTemplate($this);
            $params['sign'] = $signName;
        }
        $random = substr(uniqid(), -10);

        $params['sig'] = $this->generateSign($params, $random);

        $url = self::ENDPOINT_URL.self::ENDPOINT_METHOD.'?sdkappid='.$config->get('sdk_app_id').'&random='.$random;

        $result = $this->request('post', $url, [
            'headers' => ['Accept' => 'application/json'],
            'json' => $params,
        ]);

        if (0 != $result['result']) {
            throw new GatewayErrorException($result['errmsg'], $result['result'], $result);
        }

        return $result;
    }

    
    protected function generateSign($params, $random)
    {
        ksort($params);

        return hash('sha256', sprintf(
            'appkey=%s&random=%s&time=%s&mobile=%s',
            $this->config->get('app_key'),
            $random,
            $params['time'],
            $params['tel']['mobile']
        ), false);
    }
}
