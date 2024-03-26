<?php

namespace ModStart\Core\Util;

use Symfony\Component\HttpFoundation\IpUtils;

class IpUtil
{
    /**
     * 检查IP是否在指定的IP范围内
     * @param $ip string IP地址 例如：x.x.x.x
     * @param $ipRange string IP范围 例如：单个(x.x.x.x)掩码(x.x.x.x/x)范围(x.x.x.x-x.x.x.x)
     * @return void
     */
    public static function match4($ip, $ipRange)
    {
        // 范围
        if (strpos($ipRange, '-') !== false) {
            list($start, $end) = explode('-', $ipRange);
            return ip2long($ip) >= ip2long($start) && ip2long($ip) <= ip2long($end);
        }
        // 掩码，单个
        return IpUtils::checkIp4($ip, $ipRange);
    }
}
