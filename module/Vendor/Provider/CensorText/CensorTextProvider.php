<?php


namespace Module\Vendor\Provider\CensorText;


use ModStart\Core\Exception\BizException;

class CensorTextProvider
{
    /**
     * @var AbstractCensorTextProvider[]
     */
    private static $instances = [
        DefaultCensorTextProvider::class,
    ];

    public static function register($provider)
    {
        self::$instances[] = $provider;
    }

    /**
     * @return AbstractCensorTextProvider[]
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
        return array_build(self::all(), function ($k, $provider) {
            return [
                $provider->name(),
                $provider->title(),
            ];
        });
    }

    /**
     * @param $name
     * @return AbstractCensorTextProvider
     * @throws BizException
     */
    public static function get($name)
    {
        foreach (self::all() as $item) {
            if ($item->name() == $name) {
                return $item;
            }
        }
        return null;
    }
}