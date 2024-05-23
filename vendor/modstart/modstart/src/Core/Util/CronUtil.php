<?php

namespace ModStart\Core\Util;

use Cron\CronExpression;

class CronUtil
{
    public static function toString($cron)
    {
        $cron = trim($cron);
        if (empty($cron)) {
            return '';
        }
        $parts = explode(' ', $cron);
        if (count($parts) != 5) {
            return '解析错误';
        }
        $minute = self::toStringPart($parts[0], 0, 59, '分');
        $hour = self::toStringPart($parts[1], 0, 23, '时');
        $day = self::toStringPart($parts[2], 1, 31, '日');
        $month = self::toStringPart($parts[3], 1, 12, '月');
        $week = self::toStringPart($parts[4], 0, 6, '周');
        $result = [];
        $result[] = '每';
        if ($month) {
            $result[] = $month;
        }
        if ($week) {
            $result[] = $week;
        }
        if ($day) {
            $result[] = $day;
        }
        if ($hour) {
            $result[] = $hour;
        }
        if ($minute) {
            $result[] = $minute;
        }
        return implode('', $result);
    }

    private static function toStringPart($part, $start, $end, $unit)
    {
        if ($part == '*') {
            return '';
        }
        if (strpos($part, '*/') !== false) {
            $parts = explode('*/', $part);
            $step = $parts[1];
            return "隔{$step}{$unit}";
        }
        if (strpos($part, '/') !== false) {
            $parts = explode('/', $part);
            $start = $parts[0];
            $step = $parts[1];
            return "{$start}到{$end}隔{$step}{$unit}";
        }
        if (strpos($part, '-') !== false) {
            $parts = explode('-', $part);
            $start = $parts[0];
            $end = $parts[1];
            return "{$start}到{$end}{$unit}";
        }
        if (strpos($part, ',') !== false) {
            $parts = explode(',', $part);
            $values = array_map('intval', $parts);
            return implode('和', $values) . "{$unit}";
        }
        return "{$part}{$unit}";
    }

    public static function isValid($cron)
    {
        $cron = trim($cron);
        if (empty($cron)) {
            return false;
        }
        $parts = explode(' ', $cron);
        if (count($parts) != 5) {
            return false;
        }
        $minute = $parts[0];
        $hour = $parts[1];
        $day = $parts[2];
        $month = $parts[3];
        $week = $parts[4];
        if (!self::isValidPart($minute, 0, 59)) {
            return false;
        }
        if (!self::isValidPart($hour, 0, 23)) {
            return false;
        }
        if (!self::isValidPart($day, 1, 31)) {
            return false;
        }
        if (!self::isValidPart($month, 1, 12)) {
            return false;
        }
        if (!self::isValidPart($week, 0, 6)) {
            return false;
        }
        return true;
    }

    private static function isValidPart($part, $start, $end)
    {
        if ($part == '*') {
            return true;
        }
        if (strpos($part, '/') !== false) {
            $parts = explode('/', $part);
            if (count($parts) != 2) {
                return false;
            }
            $start = $parts[0];
            $step = $parts[1];
            if ((!is_numeric($start) && $start != '*') || !is_numeric($step)) {
                return false;
            }
            if ($start < $start || $start > $end) {
                return false;
            }
            if ($step < 1) {
                return false;
            }
            return true;
        }
        if (strpos($part, '-') !== false) {
            $parts = explode('-', $part);
            if (count($parts) != 2) {
                return false;
            }
            $start = $parts[0];
            $end = $parts[1];
            if (!is_numeric($start) || !is_numeric($end)) {
                return false;
            }
            if ($start < $start || $start > $end) {
                return false;
            }
            return true;
        }
        if (strpos($part, ',') !== false) {
            $parts = explode(',', $part);
            foreach ($parts as $part) {
                if (!is_numeric($part) || $part < $start || $part > $end) {
                    return false;
                }
            }
            return true;
        }
        if (!is_numeric($part) || $part < $start || $part > $end) {
            return false;
        }
        return true;
    }

    public static function getNextRunTimestamp($cron)
    {
        if (is_array($cron)) {
            $nextRunTime = 0;
            foreach ($cron as $c) {
                $next = CronExpression::factory($c)->getNextRunDate()->getTimestamp();
                if (!$nextRunTime || $next < $nextRunTime) {
                    $nextRunTime = $next;
                }
            }
            return $nextRunTime;
        }
        return CronExpression::factory($cron)->getNextRunDate()->getTimestamp();
    }

    public static function getNextRunDatetime($cron)
    {
        return date('Y-m-d H:i:s', self::getNextRunTimestamp($cron));
    }
}
