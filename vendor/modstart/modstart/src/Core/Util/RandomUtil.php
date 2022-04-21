<?php

namespace ModStart\Core\Util;


/**
 * Class RandomUtil
 * @package ModStart\Core\Util
 *
 * @Util 随机字符串
 */
class RandomUtil
{
    /**
     * 随机数字
     * @param $length int 长度
     * @return string 字符串
     * @Util
     */
    public static function number($length)
    {
        $pool = '0123456789';
        return substr(str_shuffle(str_repeat($pool, $length)), 0, $length);
    }

    /**
     * 随机字符串
     * @param $length int 长度
     * @return string 字符串
     * @Util
     */
    public static function string($length)
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle(str_repeat($pool, $length)), 0, $length);
    }

    /**
     * 随机可读字符串
     * @param $length int 长度
     * @return string 字符串
     * @desc 去掉0、O等相似字符
     * @Util
     */
    public static function readableString($length)
    {
        $pool = '2345678abcdefghijkmnoprstuvwxyzABCDEFGHIJKMNPQRSTUVWXYZ';
        return substr(str_shuffle(str_repeat($pool, $length)), 0, $length);
    }

    /**
     * 随机Hex字符串
     * @param $length int 长度
     * @return string 字符串
     * @Util
     */
    public static function hexString($length)
    {
        $pool = '0123456789abcdef';
        return substr(str_shuffle(str_repeat($pool, $length)), 0, $length);
    }

    /**
     * 随机小写字符串
     * @param $length int 长度
     * @return string 字符串
     * @Util
     */
    public static function lowerString($length)
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyz';
        return substr(str_shuffle(str_repeat($pool, $length)), 0, $length);
    }

    /**
     * 随机小写字符串
     * @param $length int 长度
     * @return string 字符串
     * @desc 只包含字母
     * @Util
     */
    public static function lowerChar($length)
    {
        $pool = 'abcdefghijklmnopqrstuvwxyz';
        return substr(str_shuffle(str_repeat($pool, $length)), 0, $length);
    }

    /**
     * 随机大写字符串
     * @param $length int 长度
     * @return string 字符串
     * @desc 只包含字母
     * @Util
     */
    public static function upperChar($length)
    {
        $pool = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle(str_repeat($pool, $length)), 0, $length);
    }

    /**
     * 随机大写字符串
     * @param $length int 长度
     * @return string 字符串
     * @Util
     */
    public static function upperString($length)
    {
        $pool = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle(str_repeat($pool, $length)), 0, $length);
    }

    /**
     * 随机UUID
     * @return string UUID
     * @desc 使用年月日构造
     *
     * @Util
     */
    public static function uuid()
    {
        return date('Ymd')
            . '-'
            . date('Hi')
            . '-'
            . date('s')
            . self::hexString(2)
            . '-'
            . self::hexString(4)
            . '-'
            . self::hexString(12);
    }

    public static function percent($value)
    {
        return rand(0, 99) < $value;
    }

    public static function dateCollection($length = 10)
    {
        $list = [];
        for ($i = time() - $length * TimeUtil::MINUTE_PERIOD_DAY; $i <= time(); $i += TimeUtil::MINUTE_PERIOD_DAY) {
            $list[] = date('Y-m-d', $i);
        }
        return $list;
    }

    public static function numberCollection($length = 10, $min = 10, $max = 100)
    {
        $list = [];
        for ($i = 0; $i < $length; $i++) {
            $list[] = rand($min, $max);
        }
        return $list;
    }

}
