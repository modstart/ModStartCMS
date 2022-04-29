<?php


namespace Module\Vendor\Provider\LiveStream;


use ModStart\Core\Exception\BizException;

class LiveStreamProvider
{
    /**
     * @var AbstractLiveStreamProvider[]
     */
    private static $instances = [
    ];

    public static function register($provider)
    {
        self::$instances[] = $provider;
    }

    /**
     * @return AbstractLiveStreamProvider[]
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

    public static function first()
    {
        foreach (self::all() as $provider) {
            return $provider->name();
        }
        return null;
    }

    /**
     * @param $name
     * @return AbstractLiveStreamProvider
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
