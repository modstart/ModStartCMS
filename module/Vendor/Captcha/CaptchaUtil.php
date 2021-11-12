<?php


namespace Module\Vendor\Captcha;



class CaptchaUtil
{
    
    public static function get()
    {
        static $instance = null;
        if (null === $instance) {
            $driver = config('CaptchaProviderDriver');
            if (empty($driver)) {
                $driver = DefaultCaptchaProvider::class;
            }
            $instance = app($driver);
        }
        return $instance;
    }
}
