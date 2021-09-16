<?php


namespace Module\Vendor\Provider\Notifier;


abstract class AbstractNotifierProvider
{
    abstract public function notify($biz, $title, $content, $param = []);
}
