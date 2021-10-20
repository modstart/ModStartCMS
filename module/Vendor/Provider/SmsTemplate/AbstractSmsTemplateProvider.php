<?php


namespace Module\Vendor\Provider\SmsTemplate;



abstract class AbstractSmsTemplateProvider
{
    abstract public function name();

    abstract public function title();

    abstract public function description();

    abstract public function example();
}
