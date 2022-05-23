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
        return bcdiv($value,100,2);
    }
}
