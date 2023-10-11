<?php


namespace Module\Vendor\Util;


use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\Request;
use ModStart\Core\Util\EnvUtil;
use ModStart\Core\Util\RandomUtil;

class NoneLoginOperateUtil
{
    public static function generateUrl($url, $param = [], $domainUrl = null)
    {
        if (null === $domainUrl) {
            $domainUrl = Request::domainUrl();
        }
        $urlParam = [];
        $urlParam['timestamp'] = time();
        $urlParam['nonce'] = RandomUtil::string(10);
        $urlParam['param'] = json_encode($param);
        $urlParam['sign'] = self::sign($url, $urlParam['nonce'], $urlParam['timestamp'], $urlParam['param']);
        return $domainUrl . modstart_web_url($url, $urlParam);
    }

    public static function sign($url, $nonce, $timestamp, $param)
    {
        $securityKey = EnvUtil::securityKey();
        return md5($url . ':' . $securityKey . ':' . $nonce . ':' . $timestamp . ':' . $param);
    }
}
