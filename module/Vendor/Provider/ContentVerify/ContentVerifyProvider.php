<?php


namespace Module\Vendor\Provider\ContentVerify;

/**
 * Class ContentVerifyProvider
 * @package Module\Vendor\Provider\ContentVerify
 * @deprecated delete at 2023-10-10
 */
class ContentVerifyProvider
{
    /**
     * @var AbstractContentVerifyProvider[]
     */
    private static $instances = [
    ];

    public static function register($provider)
    {
        self::$instances[] = $provider;
    }

    /**
     * @return AbstractContentVerifyProvider[]
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
     * @return AbstractContentVerifyProvider
     */
    public static function get($name)
    {
        if (empty($name)) {
            return null;
        }
        foreach (self::all() as $item) {
            if ($item->name() == $name) {
                return $item;
            }
        }
        return null;
    }

}
