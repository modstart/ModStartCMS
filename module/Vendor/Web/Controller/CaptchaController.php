<?php


namespace Module\Vendor\Web\Controller;


use Illuminate\Routing\Controller;
use ModStart\Misc\Captcha\CaptchaFacade;

class CaptchaController extends Controller
{
    public function image()
    {
        return CaptchaFacade::create('default');
    }
}
