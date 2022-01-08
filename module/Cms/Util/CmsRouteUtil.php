<?php


namespace Module\Cms\Util;


class CmsRouteUtil
{
    public static function rewrite($url, $param = [], $method = 'GET')
    {
        $request = \Illuminate\Http\Request::create($url, $method, $param);
        return app()->handle($request);
    }
}