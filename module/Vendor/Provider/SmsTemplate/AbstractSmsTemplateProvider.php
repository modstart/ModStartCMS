<?php


namespace Module\Vendor\Provider\SmsTemplate;


/**
 * Class AbstractSmsTemplateProvider
 * @package Module\Vendor\Provider\SmsTemplate
 * @since 1.6.0
 */
abstract class AbstractSmsTemplateProvider
{
    abstract public function name();

    abstract public function title();

    abstract public function description();

    abstract public function example();
}
