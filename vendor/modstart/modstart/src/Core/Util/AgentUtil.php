<?php

namespace ModStart\Core\Util;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Jenssegers\Agent\Facades\Agent;

/**
 * @Util
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
        '/YisouSpider/i' => 'Yisou',

        '/duckduckgo\\.com/i' => 'Other',
        '/DotBot/i' => 'Other',
        '/AhrefsBot/i' => 'Other',
        '/SemrushBot/i' => 'Other',
        '/GeedoBot/i' => 'Other',
        '/AwarioBot/i' => 'Other',
        '/MJ12bot/i' => 'Other',
        '/YandexBot/i' => 'Other',
        '/YandexImages/i' => 'Other',
        '/serpstatbot/i' => 'Other',
        '/NetcraftSurveyAgent/i' => 'Other',
        '/CensysInspect/i' => 'Other',
        '/Scrapy/i' => 'Other',
        '/Amazonbot/i' => 'Other',
        '/Applebot/i' => 'Other',
        '/ZoominfoBot/i' => 'Other',
        '/PetalBot/i' => 'Other',
        '/SurdotlyBot/i' => 'Other',
        '/DataForSeoBot/i' => 'Other',
        '/linkfluence/i' => 'Other',
        '/SeznamBot/i' => 'Other',
        '/Ruby/i' => 'Other',
        '/aiohttp/i' => 'Other',
        '/Twitterbot/i' => 'Other',
        '/Slackbot\\-LinkExpanding/i' => 'Other',
        '/Apache\\-HttpClient/i' => 'Other',
        '/github\\-camo/i' => 'Other',
        '/python\\-http/i' => 'Other',
        '/python\\-requests/i' => 'Other',
        '/Go\\-http\\-client/i' => 'Other',
        '/cpp\\-httplib/i' => 'Other',
        '/node\\-fetch/i' => 'Other',
        '/okhttp/i' => 'Other',
        '/msray/i' => 'Other',
        '/linkdexbot/i' => 'Other',
        '/GPTBot/i' => 'Other',
        '/crawler/i' => 'Other',
        '/curl\\/\\d+\\.\\d+\\./i' => 'Other',
        '/Java\\/\\d+\\.\\d+\\./i' => 'Other',
        '/InternetMeasurement/i' => 'Other',
        '/DingTalkBot/i' => 'Other',
        '/Vue\\-Telescope/i' => 'Other',
        '/2ip\\.io/i' => 'Other',
        '/facebookexternalhit/i' => 'Other',
        '/coccocbot\\-web/i' => 'Other',
        '/Dataprovider\\.com/i' => 'Other',
        '/Wordupindexinfo/i' => 'Other',
        '/researchscan/i' => 'Other',
        '/spider/i' => 'Other',
    ];

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
