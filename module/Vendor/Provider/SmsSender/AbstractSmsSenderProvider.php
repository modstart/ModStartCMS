<?php


namespace Module\Vendor\Provider\SmsSender;


/**
 * Class AbstractSmsSenderProvider
 * @package Module\Vendor\Provider\SmsSender
 * @since 1.6.0
 */
abstract class AbstractSmsSenderProvider
{
    abstract public function name();

    abstract public function send($phone, $template, $templateData, $param = []);
}
