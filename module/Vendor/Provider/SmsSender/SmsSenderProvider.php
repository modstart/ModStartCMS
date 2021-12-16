<?php


namespace Module\Vendor\Provider\SmsSender;

use ModStart\Core\Exception\BizException;

/**
 * Class SmsSenderProvider
 * @package Module\Vendor\Provider\SmsSender
 * @since 1.6.0
 */
class SmsSenderProvider
{
    /**
     * @var AbstractSmsSenderProvider[]
     */
    private static $instances = [
    ];

    public static function register($provider)
    {
        self::$instances[] = $provider;
    }

    /**
     * @return AbstractSmsSenderProvider[]
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
     * @return AbstractSmsSenderProvider
     * @throws BizException
     */
    public static function get($name)
    {
        foreach (self::all() as $item) {
            /** @var AbstractSmsSenderProvider $item */
            if ($item->name() == $name) {
                return $item;
            }
        }
        BizException::throws('没有找到SmsSenderProvider');
    }

    /**
     * @return bool
     * @since 1.7.0
     */
    public static function hasProvider()
    {
        $provider = app()->config->get('SmsSenderProvider');
        return !empty($provider);
    }
}
