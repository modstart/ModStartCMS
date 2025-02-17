<?php

namespace ModStart\Core\Util;

use Illuminate\Support\Str;

class EncodeUtil
{
    public static function expiredDataForever($string, $key = null)
    {
        return self::expiredData($string, $key, 0);
    }

    public static function expiredData($string, $key = null, $expireSeconds = 3600)
    {
        if (is_null($key)) {
            $key = SecureUtil::encryptKey();
        }
        $stringHex = bin2hex($string);
        $nonce = strtolower(Str::random(6));
        $timestampHex = dechex(time());
        $expireSecondsHex = dechex($expireSeconds);
        $sign = substr(md5($key . $stringHex . $nonce . $timestampHex . $expireSecondsHex), 0, 6);
        $param = [];
        $param[] = $stringHex;
        $param[] = $nonce;
        $param[] = $timestampHex;
        $param[] = $expireSecondsHex;
        $param[] = $sign;
        return join('_', $param);
    }

    /**
     * @param $string
     * @param $key
     * @return mixed
     */
    public static function expiredDataDecode($string, $key = null)
    {
        $p = explode('_', $string);
        if (count($p) != 5) {
            return null;
        }
        if (is_null($key)) {
            $key = SecureUtil::encryptKey();
        }
        $stringHex = $p[0];
        $nonce = $p[1];
        $timestampHex = $p[2];
        $expireSecondsHex = $p[3];
        $sign = $p[4];
        $signCalc = substr(md5($key . $stringHex . $nonce . $timestampHex . $expireSecondsHex), 0, 6);
        if ($sign != $signCalc) {
            return null;
        }
        $expireSeconds = hexdec($expireSecondsHex);
        if ($expireSeconds > 0) {
            $timestamp = hexdec($timestampHex);
            if (time() > $timestamp + $expireSeconds) {
                return null;
            }
        }
        return hex2bin($stringHex);
    }

    public static function md5WithSalt($password, $passwordSalt)
    {
        return md5(md5($password) . md5($passwordSalt));
    }

    public static function detectCharset($content, $checks = ['gbk', 'utf-8'])
    {
        $encoding = strtolower(mb_detect_encoding($content, $checks));
        switch ($encoding) {
            case 'cp936':
                return 'gbk';
            case 'utf-8':
                return 'utf-8';
            default:
                return $encoding;
        }
    }

    public static function toUTF8($content, $froms = ['gbk', 'utf-8'])
    {
        $encoding = mb_detect_encoding($content, $froms);
        return iconv($encoding, 'UTF-8', $content);
    }

    public static function base64UrlSafeEncode($str)
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($str));
    }

    public static function base64UrlSafeDecode($str)
    {
        return base64_decode(str_replace(['-', '_'], ['+', '/'], $str));
    }

}
