<?php

namespace Module\Member\Util;

use ModStart\Core\Input\Request;
use ModStart\Core\Util\StrUtil;
use Module\Member\Auth\MemberUser;

class UrlUtil
{
    public static function loginEnsure($url)
    {
        if (MemberUser::isLogin()) {
            return $url;
        }
        if (StrUtil::startWith($url, 'javascript:')) {
            return self::login();
        }
        return self::login($url);
    }

    public static function login($redirect = null)
    {
        if (is_null($redirect)) {
            if (Request::isAjax()) {
                $redirect = Request::headerReferer();
            } else {
                $redirect = Request::currentPageUrl();
            }
        }
        return modstart_web_url('login', [
            'redirect' => $redirect,
        ]);
    }

    public static function vip()
    {
        return modstart_web_url('member_vip');
    }
}
