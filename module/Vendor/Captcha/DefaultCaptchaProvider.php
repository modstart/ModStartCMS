<?php


namespace Module\Vendor\Captcha;


use Illuminate\Support\Facades\View;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use ModStart\Misc\Captcha\CaptchaFacade;

/**
 * Class DefaultCaptchaProvider
 * @package Module\Vendor\Captcha
 * @deprecated delete at 2023-10-04
 */
class DefaultCaptchaProvider extends AbstractCaptchaProvider
{
    public function render()
    {
        return View::make('module::Vendor.View.widget.captcha.default')->render();
    }

    public function validate()
    {
        $input = InputPackage::buildFromInput();
        $captcha = $input->getTrimString('captcha');
        if (!CaptchaFacade::check($captcha)) {
            return Response::generate(-1, '图片验证码错误', null, '[js]$(\'[data-captcha]\').click();');
        }
        return Response::generateSuccess();
    }
}
