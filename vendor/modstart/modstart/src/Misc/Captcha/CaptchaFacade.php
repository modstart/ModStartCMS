<?php

namespace ModStart\Misc\Captcha;

use Illuminate\Support\Facades\Facade;

/**
 * Class CaptchaFacade
 * @package ModStart\Misc\Captcha
 * @mixin Captcha
 */
class CaptchaFacade extends Facade {

    /**
     * @return string
     */
    protected static function getFacadeAccessor() { return 'captcha'; }

}
