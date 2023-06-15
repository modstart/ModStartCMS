<?php


namespace Module\CaptchaTecmz\Provider;


use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use Module\Vendor\Provider\Captcha\AbstractCaptchaProvider;
use Module\Vendor\Support\ResponseCodes;
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
        return View::make('module::CaptchaTecmz.View.captcha', [
            'param' => $this->param,
        ]);
    }

    public function validate()
    {
        $input = InputPackage::buildFromInput();
        $captchaKey = $input->getTrimString('captchaKey');
        if (empty($captchaKey)) {
            return Response::generate(-1, '请进行安全验证');
        }
        $ret = TecmzUtil::instance('CaptchaTecmz_')->captchaValidate($captchaKey);
        Log::info("TecmzCaptcha.Validate - " . json_encode($ret, JSON_UNESCAPED_UNICODE));
        if ($ret['code']) {
            return Response::generate(ResponseCodes::CAPTCHA_ERROR, '请重新进行安全验证');
        }
        return Response::generateSuccess();
    }

}
