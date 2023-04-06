<?php


namespace Module\Vendor\Sms;

/**
 * Class AbstractSmsSender
 * @package Module\Vendor\Sms
 * @deprecated delete at 2023-10-04
 */
abstract class AbstractSmsSender
{
    abstract protected function sendExecute($phone, $template, $templateData, $param = []);

    public function send($phone, $template, $templateData, $param = [])
    {
        return $this->sendExecute($phone, $template, $templateData, $param);
    }
}
