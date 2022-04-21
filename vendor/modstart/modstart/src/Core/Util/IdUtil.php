<?php


namespace ModStart\Core\Util;


/**
 * Class IdUtil
 * @package ModStart\Core\Util
 * @Util ID生成
 */
class IdUtil
{
    /**
     * @Util 带前缀的ID
     * @param string $group
     * @return string 生成的ID字符串
     * @desc 一般用于页面渲染时页面元素唯一ID
     * @example
     * // 将生成 Aaa_1 Aaa_2 Aaa_3
     * IdUtil::generate('Aaa')
     *
     */
    public static function generate($group = 'Default')
    {
        static $map = [];
        if (empty($map[$group])) {
            $map[$group] = 0;
        }
        $map[$group]++;
        return $group . '_' . $map[$group];
    }

    /**
     * @Util  获取64位ID
     * @return string ID字符串
     */
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
     * @Util 生成订单号
     * @return string
     * @desc 一个19位长的（BigInteger）
     * @example
     * // 生成 20210101010101+12121
     * IdUtil::generateSN()
     */
    public static function generateSN()
    {
        return date('YmdHis', time()) . sprintf('%05d', rand(0, 99999));
    }
}
