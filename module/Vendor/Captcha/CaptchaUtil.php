<?php


namespace Module\Vendor\Captcha;


/**
 * Class CaptchaUtil
 * @package Module\Vendor\Captcha
 * @deprecated delete at 2023-10-04
 */
class CaptchaUtil
{
    /**
     * @return AbstractCaptchaProvider
     */
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
