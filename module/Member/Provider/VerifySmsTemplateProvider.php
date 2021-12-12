<?php


namespace Module\Member\Provider;


use Module\Vendor\Provider\SmsTemplate\AbstractSmsTemplateProvider;

class VerifySmsTemplateProvider extends AbstractSmsTemplateProvider
{
    public function name()
    {
        return 'verify';
    }

    public function title()
    {
        return '用户验证码';
    }

    public function description()
    {
        return '验证码模板变量为 {code}';
    }

    public function example()
    {
        return '您的验证码为 {code}';
    }

}
