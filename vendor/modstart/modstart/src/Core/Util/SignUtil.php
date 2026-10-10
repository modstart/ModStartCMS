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
        // Use timing-safe strict comparison to prevent type-juggling (magic hash) bypass
        if (self::secureEquals(self::common($params, $appSecret), $sign)) {
            return true;
        }
        // rawurlencode 遵守是94年国际标准备忘录RFC 1738，
        // urlencode 实现的是传统做法，和上者的主要区别是对空格的转义是'+'而不是'%20'
        if (self::secureEquals(self::common($params, $appSecret, 'urlencode'), $sign)) {
            return true;
        }
        if (self::secureEquals(self::common($params, $appSecret, 'rawurlencode'), $sign)) {
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
            if (is_array($v)) {
                $v = json_encode($v, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            }
            if ($function) {
                $v = $function($v);
            }
            $str[] = $k . '=' . $v;
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
        if (self::secureEquals(self::commonWithoutSecret($params, $prefix), $sign)) {
            return true;
        }
        if (self::secureEquals(self::commonWithoutSecret($params, $prefix, 'rawurlencode'), $sign)) {
            return true;
        }
        return false;
    }

    /**
     * Constant-time strict string comparison.
     * Prevents MD5 type-juggling (magic hash 0e[0-9]+) and timing side-channel leaks.
     * hash_equals() is native on PHP >= 5.6 and polyfilled by symfony/polyfill-php56.
     *
     * @param $expected string 服务端计算的签名
     * @param $actual mixed 客户端提交的签名
     * @return bool
     */
    private static function secureEquals($expected, $actual)
    {
        // Only scalar values are comparable; arrays/objects are always rejected.
        if (!is_scalar($actual)) {
            return false;
        }
        return hash_equals((string)$expected, (string)$actual);
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
            if (is_array($v)) {
                $v = json_encode($v, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            }
            $str[] = $k . '=' . $function($v);
        }
        $str = join('&', $str);

        if ($prefix) {
            $str = $prefix . '&' . $str;
        }

        $sign = md5($str);

        return $sign;
    }
}
