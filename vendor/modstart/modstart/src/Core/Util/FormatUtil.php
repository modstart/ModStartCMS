<?php

namespace ModStart\Core\Util;


class FormatUtil
{
    public static function mainDomain($domain)
    {
        if (strpos($domain, '//') === 0
            || strpos($domain, 'http://') === 0
            || strpos($domain, 'https://') === 0) {
        } else {
            $domain = 'http://' . $domain;
        }
        $ret = parse_url($domain);
        if (isset($ret['host'])) {
            $domain = $ret['host'];
            if (preg_match('/^\d+\.\d+\.\d+\.\d+$/', $domain)) {
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
        return null;
    }

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

    public static function isPhone($phone)
    {
        return preg_match('/^1[0-9]{10}$/', $phone);
    }

    public static function isUUID($uuid)
    {
        return preg_match('/^[a-z0-9]{8}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{12}$/', $uuid);
    }

    public static function isUrl($url)
    {
        return preg_match('/^(http|https):\\/\\//', $url);
    }

    public static function isEmail($email)
    {
        return preg_match('/^[a-zA-Z0-9_\\-\\.]+@[a-zA-Z0-9_\\-]+[\\.a-zA-Z0-9_\\-]+$/ ', $email);
    }

    public static function isDomain($domain)
    {
        return preg_match('/([a-z0-9]([a-z0-9\\-]{0,61}[a-z0-9])?\\.)+[a-z]{2,10}/i', $domain);
    }

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
