<?php


namespace Module\Vendor\Provider\Schedule;


/**
 * Class AbstractScheduleProvider
 * @package Module\Vendor\Provider\Schedule
 */
abstract class AbstractScheduleBiz
{
    abstract public function cron();

    public function name()
    {
        return 'default';
    }

    abstract public function title();

    abstract public function run();

    protected function cronEveryMinute()
    {
        return "* * * * *";
    }

    protected function cronEvery10Minute()
    {
        return '*/10 * * * * *';
    }

    protected function cronEvery30Minute()
    {
        return '0,30 * * * * *';
    }

    /**
     * 每天整点定时
     * @param $hour int 0~23
     */
    protected function cronEveryDayHour24($hour)
    {
        return "0 $hour * * * *";
    }

    /**
     * 每天安时分定制
     * @param $hour int 0-23
     * @param $minute int 0-59
     */
    protected function cronEveryDayHour24Minute($hour, $minute)
    {
        return "$minute $hour * * * *";
    }

    protected function cronEveryHour()
    {
        return '0 * * * * *';
    }

    protected function cronEveryDay()
    {
        return '0 0 * * * *';
    }
}
