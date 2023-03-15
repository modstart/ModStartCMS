<?php


namespace Module\Member\Util;


use Module\Vendor\Provider\Captcha\CaptchaProvider;
use Module\Vendor\Provider\Captcha\DefaultCaptchaProvider;

class SecurityUtil
{
    public static function registerCaptchaProvider()
    {
        $name = modstart_config('Member_RegisterCaptchaProvider', null);
        if (empty($name)) {
            return null;
        }
        $provider = CaptchaProvider::get($name);
        if ($provider && ($provider instanceof DefaultCaptchaProvider)) {
            return null;
        }
        $provider->setParam('onValidate', 'window.__memberCheckCaptcha');
        return $provider;
    }

    public static function loginCaptchaProvider()
    {
        $name = modstart_config('loginCaptchaProvider', null);
        if (empty($name)) {
            return null;
        }
        $provider = CaptchaProvider::get($name);
        if ($provider && ($provider instanceof DefaultCaptchaProvider)) {
            return null;
        }
        return $provider;
    }
}
