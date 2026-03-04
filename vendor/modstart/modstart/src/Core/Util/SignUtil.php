<?php

namespace ModStart\Core\Util;

/**
 * @Util 接口签名工具
 */
class SignUtil
{
    /**
     * @Util 验证签名是否正确（响应多种编码方式）
     * @param $sign string 需要验证的签名
     * @param $params array 参数列表
     * @param $appSecret string 应用密钥
     * @return bool
     */
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

    /**
     * @Util 生成接口签名（将参数按 key 排序后拼接 app_secret 进行 MD5）
     * @param $params array 参数列表
     * @param $appSecret string 应用密钥
     * @param $function string|封装函数 参数处理方式（trim/urlencode/rawurlencode）
     * @param $appSecretName string app_secret 参数名称
     * @return string
     */
    public static function common($params, $appSecret, $function = 'trim', $appSecretName = 'app_secret')
    {
        ksort($params, SORT_STRING);

        $str = [];
        foreach ($params as $k => $v) {
            if ($function) {
                $v = $function($v);
            }
            $str [] = $k . '=' . $v;
        }

        $str[] = $appSecretName . '=' . $appSecret;
        $str = join('&', $str);

        $sign = md5($str);

        return $sign;
    }

    /**
     * @Util 不使用 appSecret 的签名验证
     * @param $sign string 需要验证的签名
     * @param $params array 参数列表
     * @param $prefix string|null 字符串前缀
     * @return bool
     */
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

    /**
     * @Util 不使用 appSecret 的签名生成
     * @param $params array 参数列表
     * @param $prefix string|null 字符串前缀
     * @param $function string 参数处理方式（urlencode/rawurlencode）
     * @return string
     */
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
