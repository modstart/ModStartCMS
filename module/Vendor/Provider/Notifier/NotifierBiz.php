<?php


namespace Module\Vendor\Provider\Notifier;


use Module\Vendor\Provider\BizTrait;

/**
 * Class NotifierBiz
 * @package Module\Vendor\Provider\Notifier
 *
 * @method static AbstractNotifierBiz[] listAll()
 */
class NotifierBiz
{
    use BizTrait;

    public static function registerQuick($name, $title)
    {
        self::register(new QuickNotifierBiz($name, $title));
    }
}
