<?php

namespace ModStart\Core\Util;

class SignUtil
{
    public static function check($sign, $params, $appSecret)
    {
        if ($sign == self::common($params, $appSecret)) {
            return true;
        }
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