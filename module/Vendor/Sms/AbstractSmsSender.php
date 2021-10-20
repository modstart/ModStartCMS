<?php


namespace Module\Vendor\Sms;


abstract class AbstractSmsSender
{
    abstract protected function sendExecute($phone, $template, $templateData, $param = []);

    public function send($phone, $template, $templateData, $param = [])
    {
        return $this->sendExecute($phone, $template, $templateData, $param);
    }
}
