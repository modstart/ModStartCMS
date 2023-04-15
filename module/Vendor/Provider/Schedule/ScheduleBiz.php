<?php


namespace Module\Vendor\Provider\Schedule;


/**
 * Class ScheduleProvider
 * @package Module\Vendor\Provider\Schedule
 * @since 1.5.0
 */
class ScheduleBiz
{
    /**
     * @var AbstractScheduleBiz[]
     */
    private static $instances = [
    ];

    public static function register($provider)
    {
        self::$instances[] = $provider;
    }

    /**
     * @return AbstractScheduleBiz[]
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
}
