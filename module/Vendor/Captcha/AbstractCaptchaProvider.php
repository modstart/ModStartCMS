<?php


namespace Module\Vendor\Captcha;


/**
 * Class AbstractCaptchaProvider
 * @package Module\Vendor\Captcha
 * @deprecated
 */
abstract class AbstractCaptchaProvider
{
    abstract public function render();

    abstract public function validate();
}
