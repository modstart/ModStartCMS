<?php


namespace ModStart\Core\Util;


class IdUtil
{
    public static function generate($group = 'Default')
    {
        static $map = [];
        if (empty($map[$group])) {
            $map[$group] = 0;
        }
        $map[$group]++;
        return $group . '_' . $map[$group];
    }

    public static function next64BitId()
    {
        return intval(microtime(true) * 10000) . (getmypid() % 10) . sprintf('%04d', rand(0, 9999));
    }

    public static function next64BitIdSeq($workerId = null)
    {
        static $lastTimestamp = null;
        static $seq = 0;
        static $bit = null;

        do {
            $timestamp = intval(microtime(true) * 10000);
            if ($timestamp !== $lastTimestamp) {
                $seq = 0;
            }
        } while ($seq >= 1000);
        $lastTimestamp = $timestamp;

        if ($workerId === null) {
            $workerId = getmypid();
        }
        $workerBit = ($workerId % 10);

        if (null === $bit) {
            $bit = rand(0, 9);
        }

        return $timestamp . $workerBit . $bit . sprintf('%03d', $seq++);
    }

    /**
     * 生成一个19位长的订单号（BigInteger）
     * @return string
     * @example
     * 20210101010101+12121
     */
    public static function generateSN()
    {
        return date('YmdHis', time()) . sprintf('%05d', rand(0, 99999));
    }
}
