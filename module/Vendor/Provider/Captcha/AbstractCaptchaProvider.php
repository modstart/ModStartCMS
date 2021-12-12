<?php


namespace Module\Vendor\Provider\Captcha;



abstract class AbstractCaptchaProvider
{
    protected $param = [];

    public function setParam($key, $value)
    {
        $this->param[$key] = $value;
    }

    abstract public function name();

    abstract public function title();

    abstract public function render();

    abstract public function validate();
}
