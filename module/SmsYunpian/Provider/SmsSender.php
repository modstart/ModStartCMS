<?php


namespace Module\SmsYunpian\Provider;


use ModStart\Core\Input\Response;
use Module\Vendor\Provider\SmsSender\AbstractSmsSenderProvider;
use Overtrue\EasySms\EasySms;

class SmsSender extends AbstractSmsSenderProvider
{
    const NAME = 'yunpian';
    const MODULE_NAME = 'SmsYunpian';
    const MODULE_TITLE = '云片';
    const MODULE_URL = 'http://www.yunpian.com';

    private $easySms;

    
    public function __construct()
    {
        $this->easySms = new EasySms([
            'timeout' => 30,
            'default' => [
                'strategy' => \Overtrue\EasySms\Strategies\OrderStrategy::class,
                'gateways' => [
                    'yunpian',
                ],
            ],
            'gateways' => [
                'errorlog' => [
                    'file' => '/tmp/easy-sms.log',
                ],
                'yunpian' => [
                    'api_key' => modstart_config('SmsYunpian_ApiKey'),
                    'signature' => modstart_config('SmsYunpian_Signature'),
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
        $templateId = modstart_config('SmsYunpian_Template_' . $template);
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
