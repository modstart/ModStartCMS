<?php


namespace Module\Vendor\Provider\MailSender;



abstract class AbstractMailSenderProvider
{
    abstract public function name();

    abstract public function send($email, $emailUserName, $subject, $content, $param = []);
}
