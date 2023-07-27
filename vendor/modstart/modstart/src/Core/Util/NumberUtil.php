<?php

namespace ModStart\Core\Util;


class NumberUtil
{
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

    public static function randomInt($min, $max)
    {
        if ($min == $max) {
            return $min;
        }
        return rand($min, $max);
    }

    public static function randomDecimal($min, $max)
    {
        if ($min == $max) {
            return $min;
        }
        $value = rand(intval(bcmul($min, 100)), intval(bcmul($max, 100)));
        return bcdiv($value, 100, 2);
    }

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
}
