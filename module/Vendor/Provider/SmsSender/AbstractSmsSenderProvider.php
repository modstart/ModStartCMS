<?php


namespace Module\Vendor\Provider\SmsSender;



abstract class AbstractSmsSenderProvider
{
    abstract public function name();

    abstract public function send($phone, $template, $templateData, $param = []);
}
