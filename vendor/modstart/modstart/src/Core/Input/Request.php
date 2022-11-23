<?php

namespace ModStart\Core\Input;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class Request
{

    /**
     * get path for request
     * @return string
     *
     * @example visit http://www.example.com/url/path?foo=bar -> url/path
     */
    public static function path()
    {
        return \Illuminate\Support\Facades\Request::path();
    }

    /**
     * get base path for request
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
     * get full url for request (with query string)
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
     * get full url for request (with query string)
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
     * get full url for request (without query string)
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

    /**
     * merge url queies with given array
     * @param array $pair
     * @return string
     */
    public static function mergeQueries($pair = [])
    {
        $gets = (!empty($_GET) && is_array($_GET)) ? $_GET : [];
        foreach ($pair as $k => $v) {
            $gets[$k] = $v;
        }

        $urls = [];
        foreach ($gets as $k => $v) {
            if (null === $v || '' === $v) {
                continue;
            }
            if (is_array($v)) {
                if (!isset($v[0])) {
                    continue;
                }
                if (preg_match('/^\{\w+\}$/', $v[0])) {
                    $v = $v[0];
                } else {
                    $v = urlencode($v[0]);
                }
            } else {
                $v = urlencode($v);
            }
            $urls[] = urlencode($k) . '=' . $v;
        }

        return join('&', $urls);
    }

    /**
     * get domain url
     * @return string
     */
    public static function domain()
    {
        return \Illuminate\Support\Facades\Request::server('HTTP_HOST');
    }

    /**
     * check if current request is https
     * @return bool
     */
    public static function isSecurity()
    {
        if ($forceSchema = config('modstart.forceSchema')) {
            return $forceSchema == 'https';
        }
        return \Illuminate\Support\Facades\Request::secure();
    }

    /**
     * @get current request scheme
     * @return string
     */
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
     * get current request domain url
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

    /**
     * check if current request is get
     * @return bool
     */
    public static function isGet()
    {
        return \Illuminate\Support\Facades\Request::isMethod('get');
    }

    /**
     * check if current request is post
     * @return bool
     */
    public static function isPost()
    {
        return \Illuminate\Support\Facades\Request::isMethod('post');
    }

    /**
     * check if current request is ajax
     * @return bool
     */
    public static function isAjax()
    {
        return \Illuminate\Support\Facades\Request::ajax() || self::headerGet('is-ajax');
    }

    /**
     * get laravel request instance
     * @return \Illuminate\Http\Request
     */
    public static function instance()
    {
        return \Illuminate\Support\Facades\Request::instance();
    }

    /**
     * get current request controller and action
     * @return array
     *
     * @example
     * list($controller, $action) = Request::controllerAction();
     */
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
