<?php


namespace Module\SmsTecmz\Provider;


use ModStart\Core\Input\Response;
use Module\Vendor\Provider\SmsSender\AbstractSmsSenderProvider;
use Module\Vendor\Tecmz\Tecmz;

class SmsSender extends AbstractSmsSenderProvider
{
    /** @var Tecmz */
    private $api;

    /**
     * EmailSmtpSender constructor.
     */
    public function __construct()
    {
        $this->api = Tecmz::instance(modstart_config('SmsTecmz_AppId'), modstart_config('SmsTecmz_AppSecret'));
    }

    public function name()
    {
        return 'tecmz';
    }

    public function send($phone, $template, $templateData, $param = [])
    {
        // return Response::generateSuccess();
        return $this->api->smsSend($phone, modstart_config('SmsTecmz_Template_' . $template), $templateData);
    }

}
