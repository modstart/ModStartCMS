<?php


namespace Module\Vendor\Provider\Captcha;



abstract class AbstractCaptchaProvider
{
    abstract public function name();

    abstract public function title();

    abstract public function render();

    abstract public function validate();
}
