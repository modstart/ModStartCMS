<?php


namespace Module\CaptchaTecmz\Provider;


use Illuminate\Support\Facades\View;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use Module\Vendor\Provider\Captcha\AbstractCaptchaProvider;
use Module\Vendor\Tecmz\TecmzUtil;

class TecmzCaptchaProvider extends AbstractCaptchaProvider
{
    public function name()
    {
        return 'tecmz';
    }

    public function title()
    {
        return '魔众智能验证码';
    }

    public function render()
    {
        return View::make('module::CaptchaTecmz.View.captcha');
    }

    public function validate()
    {
        $input = InputPackage::buildFromInput();
        $captchaKey = $input->getTrimString('captchaKey');
        if (empty($captchaKey)) {
            return Response::generate(-1, '请先进行安全验证', null, '[js]window.tsCaptcha.reset();');
        }
        $ret = TecmzUtil::instance('CaptchaTecmz_')->captchaValidate($captchaKey);
        if ($ret['code']) {
            return Response::generate(-1, '请重新进行安全验证', null, '[js]window.tsCaptcha.reset();');
        }
        return Response::generateSuccess();
    }

}
