<?php


namespace Module\SmsAliyun\Provider;


use ModStart\Core\Input\Response;
use Module\Vendor\Provider\SmsSender\AbstractSmsSenderProvider;
use Overtrue\EasySms\EasySms;

class SmsSender extends AbstractSmsSenderProvider
{
    const NAME = 'aliyun';
    const MODULE_NAME = 'SmsAliyun';
    const MODULE_TITLE = '阿里云';
    const MODULE_URL = 'http://www.aliyun.com';

    private $easySms;

    
    public function __construct()
    {
        $this->easySms = new EasySms([
            'timeout' => 30,
            'default' => [
                'strategy' => \Overtrue\EasySms\Strategies\OrderStrategy::class,
                'gateways' => [
                    'aliyun',
                ],
            ],
            'gateways' => [
                'errorlog' => [
                    'file' => '/tmp/easy-sms.log',
                ],
                'aliyun' => [
                    'access_key_id' => modstart_config('SmsAliyun_AccessKeyId'),
                    'access_key_secret' => modstart_config('SmsAliyun_AccessKeySecret'),
                    'sign_name' => modstart_config('SmsAliyun_SignName'),
                ],
            ],
        ]);
    }

    public function name()
    {
        return self::NAME;
    }

    public function send($phone, $template, $templateData, $param = [])
    {
        $templateId = modstart_config('SmsAliyun_Template_' . $template);
        try {
            $ret = $this->easySms->send($phone, [
                'template' => $templateId,
                'data' => $templateData,
            ]);
            if ('success' == $ret['aliyun']['status']) {
                return Response::generate(0, 'ok');
            }
            return Response::generate(-1, '发送错误');
        } catch (\Exception $e) {
            return Response::generate(-1, '发送错误');
        }
    }

}
