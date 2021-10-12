<?php

namespace ModStart\Core\Util;

class SignUtil
{
    public static function check($sign, $params, $appSecret)
    {
        if ($sign == self::common($params, $appSecret)) {
            return true;
        }
        // rawurlencode 遵守是94年国际标准备忘录RFC 1738，
        // urlencode 实现的是传统做法，和上者的主要区别是对空格的转义是'+'而不是'%20'
        if ($sign == self::common($params, $appSecret, 'urlencode')) {
            return true;
        }
        if ($sign == self::common($params, $appSecret, 'rawurlencode')) {
            return true;
        }
        return false;
    }

    public static function common($params, $appSecret, $function = 'trim', $appSecretName = 'app_secret')
    {
        ksort($params, SORT_STRING);

        $str = [];
        foreach ($params as $k => $v) {
            $str [] = $k . '=' . $function($v);
        }

        $str[] = $appSecretName . '=' . $appSecret;
        $str = join('&', $str);

        $sign = md5($str);

        return $sign;
    }

    public static function checkWithoutSecret($sign, $params, $prefix = null)
    {
        // rawurlencode 遵守是94年国际标准备忘录RFC 1738，
        // urlencode 实现的是传统做法，和上者的主要区别是对空格的转义是'+'而不是'%20'
        if ($sign == self::commonWithoutSecret($params, $prefix)) {
            return true;
        }
        if ($sign == self::commonWithoutSecret($params, $prefix, 'rawurlencode')) {
            return true;
        }
        return false;
    }

    public static function commonWithoutSecret($params, $prefix = null, $function = 'urlencode')
    {
        ksort($params, SORT_STRING);

        $str = [];
        foreach ($params as $k => $v) {
            $str [] = $k . '=' . $function($v);
        }
        $str = join('&', $str);

        if ($prefix) {
            $str = $prefix . '&' . $str;
        }

        $sign = md5($str);

        return $sign;
    }
}