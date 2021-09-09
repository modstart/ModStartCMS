<?php

namespace Module\Vendor\Oauth;

class OauthUtil
{
    public static function hasOauth()
    {
        if (self::isWechatMobileEnable()) {
            return true;
        }
        if (self::isQQEnable()) {
            return true;
        }
        if (self::isWeiboEnable()) {
            return true;
        }
        if (self::isWechatEnable()) {
            return true;
        }
        if (self::isWechatMiniProgramEnable()) {
            return true;
        }
        return false;
    }

    public static function isWechatMobileEnable()
    {
        if (modstart_config()->getWithEnv('oauthWechatMobileEnable', false)) {
            return true;
        }
        return false;
    }

    public static function isWechatMiniProgramEnable()
    {
        if (modstart_config()->getWithEnv('oauthWechatMiniProgramEnable', false)) {
            return true;
        }
        return false;
    }

    public static function isQQEnable()
    {
        if (modstart_config()->getWithEnv('oauthQQEnable', false)) {
            return true;
        }
        return false;
    }

    public static function isWechatEnable()
    {
        if (modstart_config()->getWithEnv('oauthWechatEnable', false)) {
            return true;
        }
        return false;
    }

    public static function isWeiboEnable()
    {
        if (modstart_config()->getWithEnv('oauthWeiboEnable', false)) {
            return true;
        }
        return false;
    }
}