<?php


namespace Module\Vendor\Provider\Schedule;



abstract class AbstractScheduleProvider
{
    abstract public function cron();

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

    protected function cronEveryHour()
    {
        return '0 * * * * *';
    }

    protected function cronEveryDay()
    {
        return '0 0 * * * *';
    }
}
