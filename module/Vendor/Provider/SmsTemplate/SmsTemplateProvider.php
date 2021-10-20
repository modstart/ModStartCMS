<?php


namespace Module\Vendor\Provider\SmsTemplate;

use ModStart\Support\Concern\HasFields;


class SmsTemplateProvider
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

    public static function map()
    {
        $map = [];
        foreach (self::all() as $item) {
            $map[$item->name()] = [
                'name' => $item->name(),
                'title' => $item->title(),
                'description' => $item->description(),
                'example' => $item->example(),
            ];
        }
        return $map;
    }
}
