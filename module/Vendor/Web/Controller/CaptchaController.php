<?php


namespace Module\Vendor\Web\Controller;


use Illuminate\Routing\Controller;
use Mews\Captcha\Facades\Captcha;

class CaptchaController extends Controller
{
    public function image()
    {
        return Captcha::create('formula');
    }
}
