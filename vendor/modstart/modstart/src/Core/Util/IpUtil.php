<?php

namespace ModStart\Core\Util;

use Symfony\Component\HttpFoundation\IpUtils;

/**
 * @Util IP 工具
 */
class IpUtil
{
    /**
     * @Util 判断 IP 是否在指定的 IP 范围内
     * @param $ip string IP 地址 例如：x.x.x.x
     * @param $ipRange string IP 范围 例如：单个(x.x.x.x)掩码(x.x.x.x/x)范围(x.x.x.x-x.x.x.x)
     * @return bool
     */
    public static function match4($ip, $ipRange)
    {
        $ip = trim($ip);
        // 范围
        $ipRange = str_replace('－', '-', $ipRange);
        if (strpos($ipRange, '-') !== false) {
            list($start, $end) = explode('-', $ipRange);
            $start = trim($start);
            $end = trim($end);
            return ip2long($ip) >= ip2long($start) && ip2long($ip) <= ip2long($end);
        }
        // 掩码，单个
        return IpUtils::checkIp4($ip, $ipRange);
    }
}
