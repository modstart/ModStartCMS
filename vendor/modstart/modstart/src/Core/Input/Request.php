<?php

namespace ModStart\Core\Input;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class Request
{

    /**
     * @return string
     *
     * @example visit http://www.example.com/url/path?foo=bar -> url/path
     */
    public static function path()
    {
        return \Illuminate\Support\Facades\Request::path();
    }

    /**
     * @return string
     *
     * @example visit http://www.example.com/url/path?foo=bar -> /url/path
     */
    public static function basePath()
    {
        static $path = null;
        if (null !== $path) {
            return $path;
        }
        $path = \Illuminate\Support\Facades\Request::path();
        if (empty($path)) {
            $path = '/';
        } else if (!Str::startsWith($path, '/')) {
            $path = '/' . $path;
        }
        return $path;
    }

    /**
     *
     * @return string
     *
     * @example visit http://www.example.com/url/path?foo=bar -> /url/path?foo=bar
     */
    public static function basePathWithQueries()
    {
        $url = self::basePath();
        if ($queryString = \Illuminate\Support\Facades\Request::getQueryString()) {
            $url .= "?" . $queryString;
        }
        return $url;
    }

    /**
     * @return string
     *
     * @example visit http://www.example.com/url/path?foo=bar -> http://www.example.com/url/path?foo=bar
     */
    public static function currentPageUrl()
    {
        if (\Illuminate\Support\Facades\Request::ajax()) {
            $redirect = \Illuminate\Support\Facades\Request::server('HTTP_REFERER');
        } else {
            $redirect = \Illuminate\Support\Facades\Request::fullUrl();
        }
        $redirect = self::fixFullUrlForceSchema($redirect);
        return self::fixUrlSubdir($redirect);
    }

    private static function fixFullUrlForceSchema($url)
    {
        if ($forceSchema = config('modstart.forceSchema')) {
            if (!starts_with($url, $forceSchema)) {
                $url = preg_replace('/^(http|https)/', $forceSchema, $url);
            }
        }
        return $url;
    }


    /**
     * @return string
     *
     * @example visit http://www.example.com/url/path?foo=bar -> http://www.example.com/url/path
     */
    public static function currentPageUrlWithOutQueries()
    {
        $url = \Illuminate\Support\Facades\Request::url();
        $url = self::fixFullUrlForceSchema($url);
        return self::fixUrlSubdir($url);
    }

    private static function fixUrlSubdir($url)
    {
        $subdirUrl = config('modstart.subdirUrl');
        if ($subdirUrl) {
            return str_replace(self::domainUrl(), rtrim($subdirUrl, '/'), $url);
        }
        return $url;
    }

    public static function mergeQueries($pair = [])
    {
        $gets = (!empty($_GET) && is_array($_GET)) ? $_GET : [];
        foreach ($pair as $k => $v) {
            $gets[$k] = $v;
        }

        $urls = [];
        foreach ($gets as $k => $v) {
            if (null === $v) {
                continue;
            }
            if (is_array($v)) {
                $v = $v[0];
            } else {
                $v = urlencode($v);
            }
            $urls[] = "$k=" . $v;
        }

        return join('&', $urls);
    }

    public static function domain()
    {
        return \Illuminate\Support\Facades\Request::server('HTTP_HOST');
    }

    public static function isSecurity()
    {
        if ($forceSchema = config('modstart.forceSchema')) {
            return $forceSchema == 'https';
        }
        return \Illuminate\Support\Facades\Request::secure();
    }

    public static function schema()
    {
        static $schema = null;
        if (null === $schema) {
            $forceSchema = config('modstart.forceSchema', null);
            if ($forceSchema) {
                return $forceSchema;
            }
            if (\Illuminate\Support\Facades\Request::secure()) {
                $schema = 'https';
            } else {
                $schema = 'http';
            }
        }
        return $schema;
    }

    /**
     * 返回当前系统的协议和域名
     *
     * @return string
     */
    public static function domainUrl($subdirFix = false)
    {
        $url = self::schema() . '://' . self::domain();
        if ($subdirFix) {
            return self::fixUrlSubdir($url);
        }
        return $url;
    }

    public static function isPost()
    {
        return \Illuminate\Support\Facades\Request::isMethod('post');
    }

    public static function isAjax()
    {
        return \Illuminate\Support\Facades\Request::ajax();
    }

    /**
     * @return \Illuminate\Http\Request
     */
    public static function instance()
    {
        return \Illuminate\Support\Facades\Request::instance();
    }

    public static function getControllerAction()
    {
        $routeAction = Route::currentRouteAction();
        $pieces = explode('@', $routeAction);
        if (isset($pieces[0])) {
            $controller = $pieces[0];
        } else {
            $controller = null;
        }
        if (isset($pieces[1])) {
            $action = $pieces[1];
        } else {
            $action = null;
        }
        if (empty($controller)) {
            return [null, null];
        }
        if (!Str::startsWith($controller, '\\')) {
            $controller = '\\' . $controller;
        }
        return [$controller, $action];
    }

    public static function headerGet($key, $defaultValue = null)
    {
        return self::instance()->header($key, $defaultValue);
    }

    public static function headerSet($key, $value)
    {
        self::instance()->headers->set($key, $value);
    }

    public static function headerReferer()
    {
        return self::headerGet('referer');
    }

    public static function headers()
    {
        return self::instance()->headers->all();
    }

    public static function ip()
    {
        return self::instance()->ip();
    }

    public static function server($name)
    {
        return self::instance()->server($name);
    }
}
