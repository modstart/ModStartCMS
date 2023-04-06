<?php


namespace Module\Vendor\Captcha;


/**
 * Class AbstractCaptchaProvider
 * @package Module\Vendor\Captcha
 * @deprecated delete at 2023-10-04
 */
abstract class AbstractCaptchaProvider
{
    abstract public function render();

    abstract public function validate();
}
