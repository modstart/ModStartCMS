<?php


namespace Module\Vendor\Provider\SmsSender;

use ModStart\Core\Exception\BizException;


class SmsSenderProvider
{
    
    private static $instances = [
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

    
    public static function get($name)
    {
        foreach (self::all() as $item) {
            
            if ($item->name() == $name) {
                return $item;
            }
        }
        BizException::throws('没有找到SmsSenderProvider');
    }

    
    public static function hasProvider()
    {
        $provider = app()->config->get('SmsSenderProvider');
        return !empty($provider);
    }
}
