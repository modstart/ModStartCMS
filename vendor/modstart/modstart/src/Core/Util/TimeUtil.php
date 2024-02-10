<?php

namespace ModStart\Core\Util;


class TimeUtil
{
    const FORMAT_DATE = 'Y-m-d';
    const FORMAT_DATETIME = 'Y-m-d H:i:s';

    const PERIOD_YEAR = 24 * 3600 * 365;
    const PERIOD_MONTH = 24 * 3600 * 30;
    const PERIOD_WEEK = 24 * 3600 * 7;
    const PERIOD_DAY = 24 * 3600;
    const PERIOD_HOUR = 3600;
    const PERIOD_MINITE = 60;

    const MINUTE_PERIOD_YEAR = 24 * 60 * 365;
    const MINUTE_PERIOD_MONTH = 24 * 60 * 30;
    const MINUTE_PERIOD_WEEK = 24 * 60 * 7;
    const MINUTE_PERIOD_DAY = 24 * 60;
    const MINUTE_PERIOD_HOUR = 60;
    const MINUTE_PERIOD_MINITE = 1;

    public static function formatTimeLength($seconds)
    {
        static $ts = null;
        if (null === $ts) {
            $ts = strtotime('2020-01-01 00:00:00');
        }
        $hour = intval($seconds / self::PERIOD_HOUR);
        $minute = intval(($seconds % self::PERIOD_HOUR) / self::PERIOD_MINITE);
        $second = intval($seconds % self::PERIOD_MINITE);
        $pcs = [];
        if ($hour) {
            $pcs[] = sprintf('%02d', $hour);
        }
        $pcs[] = sprintf('%02d', $minute);
        $pcs[] = sprintf('%02d', $second);
        return implode(':', $pcs);
    }

    public static function yesterdayDate()
    {
        return date('Y-m-d', time() - self::PERIOD_DAY);
    }

    public static function tomorrowDate()
    {
        return date('Y-m-d', time() + self::PERIOD_DAY);
    }

    public static function yesterdayStart()
    {
        return date('Y-m-d 00:00:00', time() - self::PERIOD_DAY);
    }

    public static function yesterdayEnd()
    {
        return date('Y-m-d 23:59:59', time() - self::PERIOD_DAY);
    }

    public static function todayStart()
    {
        return date('Y-m-d 00:00:00', time());
    }

    public static function todayEnd()
    {
        return date('Y-m-d 23:59:59', time());
    }

    public static function humanTimeLength($timeSeconds, $lang = 'zh')
    {
        $langMap = [
            'zh' => [
                'd' => '天',
                'h' => '小时',
                'm' => '分钟',
                's' => '秒',
            ],
            'en' => [
                'd' => 'd',
                'h' => 'h',
                'm' => 'm',
                's' => 's',
            ],
        ];
        $pcs = [];
        if ($timeSeconds >= self::PERIOD_DAY) {
            $v = intval($timeSeconds / self::PERIOD_DAY);
            $pcs[] = $v . $langMap[$lang]['d'];
            $timeSeconds %= self::PERIOD_DAY;
        }
        if ($timeSeconds >= self::PERIOD_HOUR) {
            $v = intval($timeSeconds / self::PERIOD_HOUR);
            $pcs[] = $v . $langMap[$lang]['h'];
            $timeSeconds %= self::PERIOD_HOUR;
        }
        if ($timeSeconds >= self::PERIOD_MINITE) {
            $v = intval($timeSeconds / self::PERIOD_MINITE);
            $pcs[] = $v . $langMap[$lang]['m'];
            $timeSeconds %= self::PERIOD_MINITE;
        }
        if ($timeSeconds > 0) {
            $pcs[] = $timeSeconds . $langMap[$lang]['s'];
        }
        return join('', $pcs);
    }

    public static function date()
    {
        return date(self::FORMAT_DATE, time());
    }

    public static function now()
    {
        return date(self::FORMAT_DATETIME, time());
    }

    public static function dateCollection($startDate, $endDate)
    {
        if (!is_numeric($startDate)) {
            $startDate = strtotime($startDate);
        }
        if (!is_numeric($endDate)) {
            $endDate = strtotime($endDate);
        }
        if ($startDate > $endDate || $startDate <= 0 || $endDate <= 0) {
            return [];
        }
        $date = [];
        for ($i = $startDate; $i <= $endDate; $i += self::PERIOD_DAY) {
            $date[] = date('Y-m-d', $i);
        }
        return $date;
    }

    public static function format($timestamp, $format = null)
    {
        if (null === $format) {
            $format = self::FORMAT_DATETIME;
        }
        return date($format, $timestamp);
    }

    /**
     * 获取是否为period之前的时间
     *
     * @param $timestamp
     * @param $period
     *
     * @return true|false
     */
    public static function isBefore($timestamp, $period)
    {
        return $timestamp < time() - $period;
    }

    /**
     * 获取period之前的时间戳
     *
     * @param $period
     * @return int
     */
    public static function getBeforeTimestamp($period)
    {
        return time() - $period;
    }

    /**
     * 获取period之前的时间
     *
     * @param $period
     * @return int
     */
    public static function getBeforeDatetime($period)
    {
        return date(self::FORMAT_DATETIME, self::getBeforeTimestamp($period));
    }

    /**
     * 判断一个日期时间是否为空
     * 经常会出现 0000-00-00 00:00:00 的日期,这样判断就不为空,会发生误判
     *
     * @param $datetime
     *
     * @return boolean
     */
    public static function isDatetimeEmpty($datetime)
    {
        $timestamp = strtotime($datetime);
        if (empty($timestamp) || $timestamp < 0) {
            return true;
        }
        return false;
    }

    /**
     * 判断一个日期时间是否为空
     * 经常会出现 0000-00-00 的日期,这样判断就不为空,会发生误判
     *
     * @param $date
     *
     * @return boolean
     */
    public static function isDateEmpty($date)
    {
        $timestamp = strtotime($date);
        if (empty($timestamp) || $timestamp < 0) {
            return true;
        }
        return false;
    }

    public static function isTimeEmpty($time)
    {
        $timestamp = strtotime('2019-01-01 ' . $time);
        if (empty($timestamp) || $timestamp < 0) {
            return true;
        }
        return false;
    }


    public static function isDateExpired($expire)
    {
        if (self::isDateEmpty($expire)) {
            return false;
        }
        if (strtotime($expire) < time()) {
            return true;
        }
        return false;
    }


    public static function nextExpireTimestamp($current, $plusSeconds, $format = 'Y-m-d')
    {
        $ts = time();
        $i = strtotime($current);
        if (!empty($current) && $i > 0) {
            $ts = $i + $plusSeconds;
        } else {
            $ts += $plusSeconds;
        }
        return date($format, $ts);
    }

    public static function isInRange($start, $end)
    {
        $ts = time();
        if (!self::isDatetimeEmpty($start)) {
            $start = strtotime($start);
            if ($ts < $start) {
                return false;
            }
        }
        if (!self::isDatetimeEmpty($end)) {
            $end = strtotime($end);
            if ($ts > $end) {
                return false;
            }
        }
        return true;
    }

    /**
     * 将周期转换为秒
     * 25:12:00 转换为
     * @param $period
     * @return int
     */
    public static function periodToSecond($period)
    {
        $seconds = 0;
        $pcs = explode(':', $period);
        if (isset($pcs[0])) {
            $seconds += intval($pcs[0]) * 3600;
        }
        if (isset($pcs[1])) {
            $seconds += intval($pcs[1]) * 60;
        }
        if (isset($pcs[2])) {
            $seconds += intval($pcs[1]);
        }
        return $seconds;
    }

    public static function microtime()
    {
        return intval(microtime(true) * 1000000);
    }

    public static function millitime()
    {
        return intval(microtime(true) * 1000);
    }

    private static $monitor = [];

    public static function monitorTick($name = 'Default')
    {
        if (!isset(self::$monitor[$name])) {
            self::$monitor[$name] = self::millitime();
        }
        return self::millitime() - self::$monitor[$name];
    }

}
