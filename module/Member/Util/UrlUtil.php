<?php

namespace Module\Member\Util;

use ModStart\Core\Input\Request;

class UrlUtil
{
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
}
