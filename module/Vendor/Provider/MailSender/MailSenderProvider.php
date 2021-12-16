<?php


namespace Module\Vendor\Provider\MailSender;

use ModStart\Core\Exception\BizException;

/**
 * Class MailSenderProvider
 * @package Module\Vendor\Provider\MailSender
 * @since 1.7.0
 */
class MailSenderProvider
{
    /**
     * @var AbstractMailSenderProvider[]
     */
    private static $instances = [
    ];

    public static function register($provider)
    {
        self::$instances[] = $provider;
    }

    /**
     * @return AbstractMailSenderProvider[]
     */
    public static function all()
    {
        foreach (self::$instances as $k => $v) {
            if ($v instanceof \Closure) {
                self::$instances[$k] = call_user_func($v);
            } else if (is_string($v)) {
                self::$instances[$k] = app($v);
            }
        }
        return self::$instances;
    }

    /**
     * @param $name
     * @return AbstractMailSenderProvider
     * @throws BizException
     */
    public static function get($name)
    {
        foreach (self::all() as $item) {
            /** @var AbstractMailSenderProvider $item */
            if ($item->name() == $name) {
                return $item;
            }
        }
        BizException::throws('没有找到MailSenderProvider');
    }

    /**
     * @return bool
     */
    public static function hasProvider()
    {
        $provider = app()->config->get('EmailSenderProvider');
        return !empty($provider);
    }
}
