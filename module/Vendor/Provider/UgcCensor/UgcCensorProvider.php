<?php


namespace Module\Vendor\Provider\UgcCensor;


use ModStart\Core\Exception\BizException;

class UgcCensorProvider
{
    /**
     * @var AbstractUgcCensorProvider[]
     */
    private static $instances = [
    ];

    public static function register($provider)
    {
        self::$instances[] = $provider;
    }

    /**
     * @return AbstractUgcCensorProvider[]
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
     * @return AbstractUgcCensorProvider
     * @throws BizException
     */
    public static function get($name)
    {
        foreach (self::all() as $item) {
            if ($item->name() == $name) {
                return $item;
            }
        }
        BizException::throws('没有找到AbstractUgcCensorProvider');
    }
}