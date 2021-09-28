<?php


namespace Module\Vendor\Provider\HomePage;


class HomePageProvider
{
    
    private static $instances = [
        DefaultHomePageProvider::class,
    ];

    public static function register($provider)
    {
        self::$instances[] = $provider;
    }

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
}
