<?php

namespace ModStart\Core\Util;

use Illuminate\Support\Facades\Request;
use Jenssegers\Agent\Facades\Agent;

/**
 * @Util 客户端
 */
class AgentUtil
{
    /**
     * @Util 获取浏览器UserAgent
     * @return string
     */
    public static function getUserAgent()
    {
        return Request::header('User-Agent');
    }

    /**
     * @Util 判断浏览器类型
     * @return string pc, mobile
     */
    public static function device()
    {
        if (self::isMobile()) {
            return 'mobile';
        }
        return 'pc';
    }

    /**
     * @Util 判断是否是微信浏览器
     * @return bool
     */
    public static function isWechat()
    {
        static $isWechat = null;
        if (null === $isWechat) {
            $userAgent = Request::header('User-Agent');
            if (strpos($userAgent, 'MicroMessenger') !== false) {
                $isWechat = true;
            } else {
                $isWechat = false;
            }
        }
        return $isWechat;
    }

    /**
     * @Util 判断是否是手机浏览器
     * @return bool
     */
    public static function isMobile()
    {
        return Agent::isPhone() || Agent::isTablet();
    }

    /**
     * @Util 判断是否是电脑浏览器
     * @return bool
     */
    public static function isPC()
    {
        return !Agent::isPhone() && !Agent::isTablet();
    }

    private static $robots = [

        '/Googlebot/i' => 'Google',
        '/Baiduspider/i' => 'Baidu',
        '/360Spider/i' => '360',
        '/Sogou/i' => 'Sogou',
        '/bingbot/i' => 'Bing',
        '/Bytespider/i' => 'TouTiao',

        '/crawler/i' => 'Other',
        '/spider/i' => 'Other',
        // 通用匹配 XxxBot
        '/(?:^|[\\W])\\w+bot[\\W]*/i' => 'Other',
        '/Detector/' => 'Other',

        // 其他一些爬虫
        '/YandexImages/i' => 'Other',
        '/CensysInspect/i' => 'Other',
        '/GoogleOther/i' => 'Other',
        '/duckduckgo\\.com/i' => 'Other',
        '/Dataprovider\\.com/i' => 'Other',
        '/NetcraftSurveyAgent/i' => 'Other',
        '/Scrapy/i' => 'Other',
        '/linkfluence/i' => 'Other',
        '/github\\-camo/i' => 'Other',
        '/msray/i' => 'Other',
        '/InternetMeasurement/i' => 'Other',
        '/Vue\\-Telescope/i' => 'Other',
        '/2ip\\.io/i' => 'Other',
        '/facebookexternalhit/i' => 'Other',
        '/Wordupindexinfo/i' => 'Other',
        '/researchscan/i' => 'Other',
        '/woorankreview/i' => 'Other',
        '/Avant Browser/i' => 'Other',

        // 一些语言库、命令行
        '/Wget\\/\\d+/i' => 'Other',
        '/python\\-(http|requests|urllib)/i' => 'Other',
        '/curl\\/\\d+\\.\\d+\\./i' => 'Other',
        '/Java\\/\\d+\\.\\d+\\./i' => 'Other',
        '/Ruby/i' => 'Other',
        '/aiohttp/i' => 'Other',
        '/cpp\\-httplib/i' => 'Other',
        '/node\\-fetch/i' => 'Other',
        '/Go\\-http\\-client/i' => 'Other',
        '/okhttp/i' => 'Other',
        '/GuzzleHttp/i' => 'Other',
        '/Apache\\-HttpClient/i' => 'Other',
    ];

    /**
     * @Util 获取机器人类型
     * @param $userAgent string|null 浏览器UserAgent，为空时自动获取
     * @return string|null 机器人名称，非机器人时返回null
     */
    public static function detectRobot($userAgent = null)
    {
        if (null === $userAgent) {
            $userAgent = AgentUtil::getUserAgent();
        }
        foreach (self::$robots as $regex => $robot) {
            if (preg_match($regex, $userAgent)) {
                return $robot;
            }
        }
        return null;
    }

}
