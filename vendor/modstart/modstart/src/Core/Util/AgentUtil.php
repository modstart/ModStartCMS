<?php

namespace ModStart\Core\Util;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Jenssegers\Agent\Facades\Agent;

class AgentUtil
{
    public static function getUserAgent()
    {
        return Request::header('User-Agent');
    }

    public static function device()
    {
        if (self::isMobile()) {
            return 'mobile';
        }
        return 'pc';
    }

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

    public static function isMobile()
    {
        return Agent::isPhone() || Agent::isTablet();
    }

    public static function isPC()
    {
        return !Agent::isPhone() && !Agent::isTablet();
    }

}