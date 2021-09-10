<?php


namespace Module\Vendor\Captcha;


abstract class AbstractCaptchaProvider
{
    abstract public function render();

    abstract public function validate();
}
