<?php


namespace Module\Vendor\Provider\Captcha;

use ModStart\Core\Exception\BizException;

/**
 * Class CaptchaProvider
 * @package Module\Vendor\Provider\Captcha
 * @since 1.6.0
 */
class CaptchaProvider
{
    /**
     * @var AbstractCaptchaProvider[]
     */
    private static $instances = [
        DefaultCaptchaProvider::class,
    ];

    public static function register($provider)
    {
        self::$instances[] = $provider;
    }

    /**
     * @return AbstractCaptchaProvider[]
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

    public static function nameTitleMap()
    {
        return array_build(self::all(), function ($k, $v) {
            /** @var AbstractCaptchaProvider $v */
            return [
                $v->name(),
                $v->title()
            ];
        });
    }

    /**
     * @param $name
     * @return AbstractCaptchaProvider
     * @throws BizException
     */
    public static function get($name)
    {
        foreach (self::all() as $item) {
            /** @var AbstractCaptchaProvider $item */
            if ($item->name() == $name) {
                return $item;
            }
        }
        return null;
    }

    /**
     * @return bool
     * @since 1.7.0
     */
    public static function hasProvider()
    {
        $provider = app()->config->get('CaptchaProvider');
        return !empty($provider);
    }
}
