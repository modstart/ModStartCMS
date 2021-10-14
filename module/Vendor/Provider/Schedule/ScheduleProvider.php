<?php


namespace Module\Vendor\Provider\Schedule;


use Illuminate\Console\Scheduling\Schedule;


class ScheduleProvider
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

    public static function call(Schedule $schedule)
    {
        foreach (ScheduleProvider::all() as $provider) {
            
            $schedule->call(function () use ($provider) {
                call_user_func([$provider, 'run']);
            })->cron($provider->cron());
        }
    }
}
