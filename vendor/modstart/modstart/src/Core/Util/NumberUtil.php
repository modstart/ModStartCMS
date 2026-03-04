<?php

namespace ModStart\Core\Util;


/**
 * @Util 数字工具
 */
class NumberUtil
{
    /**
     * @Util 十进制数字转换为 62 进制字符串
     * @param $num int|string 十进制整数
     * @return string
     */
    public static function decToD62($num)
    {
        $to = 62;
        $dict = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $ret = '';
        do {
            $ret = $dict[bcmod($num, $to)] . $ret;
            $num = bcdiv($num, $to);
        } while ($num > 0);
        return $ret;
    }

    /**
     * @Util 将62 进制字符串转换为十进制数字
     * @param $num string 62 进制字符串
     * @return string
     */
    public static function d62ToDec($num)
    {
        $from = 62;
        $num = strval($num);
        $dict = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $len = strlen($num);
        $dec = 0;
        for ($i = 0; $i < $len; $i++) {
            $pos = strpos($dict, $num[$i]);
            $dec = bcadd(bcmul(bcpow($from, $len - $i - 1), $pos), $dec);
        }
        return $dec;
    }

    /**
     * @Util 生成指定范围内的随机整数
     * @param $min int 最小值
     * @param $max int 最大值
     * @return int
     */
    public static function randomInt($min, $max)
    {
        if ($min == $max) {
            return $min;
        }
        return rand($min, $max);
    }

    /**
     * @Util 生成指定范围内的随机小数（保留两位小数）
     * @param $min float 最小值
     * @param $max float 最大值
     * @return string
     */
    public static function randomDecimal($min, $max)
    {
        if ($min == $max) {
            return $min;
        }
        $value = rand(intval(bcmul($min, 100)), intval(bcmul($max, 100)));
        return bcdiv($value, 100, 2);
    }

    /**
     * @Util 将数字转换为中文数字（如一、二三...)
     * @param $number int 数字
     * @return string
     */
    public static function numToZH($number)
    {
        $chineseNumber = ['零', '一', '二', '三', '四', '五', '六', '七', '八', '九'];
        $chineseUnit = ['', '十', '百', '千', '万', '亿'];

        if ($number == 0) {
            return $chineseNumber[0];
        }

        $strNumber = strval($number);
        $strLen = strlen($strNumber);
        $result = '';

        for ($i = 0; $i < $strLen; $i++) {
            $digit = (int)$strNumber[$i];
            $unit = $strLen - $i - 1;

            if ($digit != 0) {
                $result .= $chineseNumber[$digit] . $chineseUnit[$unit];
            } else {
                // 处理零的情况，避免出现连续多个零
                if ($result[strlen($result) - 1] !== $chineseNumber[0]) {
                    $result .= $chineseNumber[$digit];
                }
            }
        }

        // 处理十位数以一开头的情况（如：一十一）
        if ($strLen > 1 && $strNumber[0] == 1 && $result[0] == $chineseNumber[1]) {
            $result = substr($result, 1);
        }

        return $result;
    }

    /**
     * @Util 对小数按指定折扣比进行计算
     * @param $decimal float 原始小数 （如价格）
     * @param $discount int 折扣比例（0~100，如 80 表示 8 折）
     * @return string
     */
    public static function discountDecimal($decimal, $discount)
    {
        if ($decimal < 0.01) {
            return $decimal;
        }
        if ($discount >= 100 || $discount <= 0) {
            return $decimal;
        }
        $decimal = bcdiv(bcmul($decimal, $discount), 100, 2);
        if ($decimal < 0.01) {
            $decimal = '0.01';
        }
        return $decimal;
    }

    /**
     * @Util 对整数按指定折扣比进行计算
     * @param $number int 原始数量
     * @param $discount int 折扣比例（0~100，如 80 表示 80%）
     * @return int
     */
    public static function discountNumber($number, $discount)
    {
        if ($number < 1) {
            return $number;
        }
        if ($discount >= 100 || $discount <= 0) {
            return $number;
        }
        $number = intval($number * $discount / 100);
        if ($number < 1) {
            $number = 1;
        }
        return $number;
    }
}
