<?php


namespace Module\Vendor\Provider\Captcha;


/**
 * Class AbstractCaptchaProvider
 * @package Module\Vendor\Provider\Captcha
 * @since 1.6.0
 */
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
