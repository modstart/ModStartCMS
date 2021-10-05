<?php


namespace Module\Vendor\Provider;

trait ProviderTrait
{
    
    private static $list = [];

    public static function register($provider)
    {
        self::$list[] = $provider;
    }

    private static function listAll()
    {
        foreach (self::$list as $k => $v) {
            if ($v instanceof \Closure) {
                self::$list[$k] = call_user_func($v);
            } else if (is_string($v)) {
                self::$list[$k] = app($v);
            }
        }
        return self::$list;
    }

    private static function getByName($name)
    {
        foreach (self::all() as $item) {
            if ($item->name() == $name) {
                return $item;
            }
        }
        return null;
    }

    public static function allMap()
    {
        return array_build(self::all(), function ($k, $v) {
            return [
                $v->name(), $v->title()
            ];
        });
    }
}
