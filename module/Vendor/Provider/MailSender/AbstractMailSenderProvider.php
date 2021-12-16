<?php


namespace Module\Vendor\Provider\MailSender;


/**
 * Class AbstractMailSenderProvider
 * @package Module\Vendor\Provider\MailSender
 * @since 1.7.0
 */
abstract class AbstractMailSenderProvider
{
    abstract public function name();

    abstract public function send($email, $emailUserName, $subject, $content, $param = []);
}
