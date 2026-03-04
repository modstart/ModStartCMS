<?php

namespace ModStart\Core\Util;


/**
 * @Util 格式化工具
 */
class FormatUtil
{
    /**
     * @Util 从 URL 中提取域名（含端口）
     * @param $url string URL 字符串
     * @return string|null
     */
    public static function domain($url)
    {
        if (strpos($url, '//') === 0
            || strpos($url, 'http://') === 0
            || strpos($url, 'https://') === 0) {
        } else {
            $url = 'http://' . $url;
        }
        $ret = parse_url($url);
        if (isset($ret['host'])) {
            $host = [];
            $host[] = $ret['host'];
            if (isset($ret['port'])) {
                $host[] = $ret['port'];
            }
            return join(':', $host);
        }
        return null;
    }

    /**
     * @Util 从 URL 或域名中提取主域名（不含子域名）
     * @param $url string URL 字符串或域名
     * @return string|null
     */
    public static function mainDomain($url)
    {
        if (strpos($url, '//') === 0
            || strpos($url, 'http://') === 0
            || strpos($url, 'https://') === 0) {
            $domain = self::domain($url);
        } else {
            $domain = $url;
        }
        if (empty($domain)) {
            return null;
        }
        if (preg_match('/^\d+\.\d+\.\d+\.\d+$/', $domain)) {
            return $domain;
        }
        if (preg_match('/^(\d+\.\d+\.\d+\.\d+):(\d+)$/', $domain, $mat)) {
            $port = $mat[2];
            if (in_array($port, [80, 443])) {
                return $mat[1];
            }
            return $domain;
        }
        $pcs = [];
        foreach (array_reverse(explode('.', $domain)) as $p) {
            if (in_array($p, ['cn', 'com', 'org', 'gov', 'edu'])) {
                $pcs[] = $p;
                continue;
            } else {
                $pcs[] = $p;
            }
            if (count($pcs) >= 2) {
                break;
            }
        }
        return join('.', array_reverse($pcs));
    }

    /**
     * @Util 格式化电话号码（去除分隔符、区号等审核格式）
     * @param $number string 原始电话号码
     * @return string|null 格式化后的纴数字符串，不合法返回 null
     */
    public static function telephone($number)
    {
        $number = str_replace([
            '+86',
            '+',
            ' ',
            '(',
            ')',
            '-',
            '（',
            '）',
            '',
            ' ',
            '　',
            '"',
            ';',
            "\t",
        ], '', $number);
        $number = trim($number);
        if (!preg_match('/^[0-9]{3,20}$/', $number)) {
            return null;
        }
        return $number;
    }

    /**
     * @Util 判断是否为手机号
     * @param $phone string 号码
     * @return bool
     */
    public static function isPhone($phone)
    {
        return preg_match('/^1[0-9]{10}$/', $phone);
    }

    /**
     * @Util 判断是否为 UUID 格式
     * @param $uuid string 字符串
     * @return bool
     */
    public static function isUUID($uuid)
    {
        return preg_match('/^[a-z0-9]{8}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{12}$/', $uuid);
    }

    /**
     * @Util 判断是否为 HTTP/HTTPS URL
     * @param $url string 字符串
     * @return bool
     */
    public static function isUrl($url)
    {
        return preg_match('/^(http|https):\\/\\//', $url);
    }

    /**
     * @Util 判断是否为合法电子邮件地址
     * @param $email string 字符串
     * @return bool
     */
    public static function isEmail($email)
    {
        return preg_match('/^[a-zA-Z0-9_\\-\\.]+@[a-zA-Z0-9_\\-]+[\\.a-zA-Z0-9_\\-]+$/ ', $email);
    }

    /**
     * @Util 判断是否为合法域名
     * @param $domain string 字符串
     * @return bool
     */
    public static function isDomain($domain)
    {
        return preg_match('/([a-z0-9]([a-z0-9\\-]{0,61}[a-z0-9])?\\.)+[a-z]{2,10}/i', $domain);
    }

    /**
     * @Util 判断金额是否合法（0.01 至 1,000,000）
     * @param $money float 金额
     * @return bool
     */
    public static function isMoney($money)
    {
        if ($money < 0.01) {
            return false;
        }
        if ($money > 10000 * 100) {
            return false;
        }
        return true;
    }
}
