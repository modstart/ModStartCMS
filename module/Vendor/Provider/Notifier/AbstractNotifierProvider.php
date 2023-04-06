<?php


namespace Module\Vendor\Provider\Notifier;

/**
 * 消息通知抽象类，用于实现不同的消息通知方式
 * Class AbstractNotifierProvider
 * @package Module\Vendor\Provider\Notifier
 */
abstract class AbstractNotifierProvider
{
    public function name()
    {
        return null;
    }

    public function title()
    {
        return null;
    }

    abstract public function notify($biz, $title, $content, $param = []);
}
