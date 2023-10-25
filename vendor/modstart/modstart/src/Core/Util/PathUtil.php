<?php

namespace ModStart\Core\Util;

use Illuminate\Support\Str;
use ModStart\Core\Input\Request;

class PathUtil
{
    public static function isPublicNetPath($path)
    {
        $prefixs = [
            '//',
            'http://',
            'https://',
        ];
        foreach ($prefixs as $prefix) {
            if (Str::startsWith($path, $prefix)) {
                return true;
            }
        }
        return false;
    }

    public static function fix($path, $cdn = null)
    {
        if (Str::startsWith($path, 'http://') || Str::startsWith($path, 'https://') || Str::startsWith($path, '//')) {
            return $path;
        }
        if (!Str::startsWith($path, '/')) {
            $path = '/' . $path;
        }
        if ($cdn === null) {
            return $path;
        }
        if (Str::endsWith($cdn, '/')) {
            $cdn = substr($cdn, 0, strlen($cdn) - 1);
        }
        return $cdn . $path;
    }

    public static function fixOrDefault($path, $default, $cdn = null)
    {
        if (empty($path)) {
            return self::fix($default, $cdn);
        }
        return self::fix($path, $cdn);
    }

    public static function fixFull($path, $cdn = null, $schema = null)
    {
        if (Str::startsWith($path, 'http://') || Str::startsWith($path, 'https://')) {
            return $path;
        }
        if (null === $schema) {
            $schema = Request::schema();
        }
        if (Str::startsWith($path, '//')) {
            return $schema . ':' . $path;
        }
        if (!Str::startsWith($path, '/')) {
            $path = '/' . $path;
        }
        if ($cdn === null) {
            $cdn = $schema . '://' . Request::domain();
        }
        if (Str::endsWith($cdn, '/')) {
            $cdn = substr($cdn, 0, strlen($cdn) - 1);
        }
        return $cdn . $path;
    }

    public static function fixFullOrDefault($path, $default, $cdn = null, $schema = null)
    {
        if (empty($path)) {
            return self::fixFull($default, $cdn, $schema);
        }
        return self::fixFull($path, $cdn, $schema);
    }

    /**
     * 将外网地址转换为内网地址
     * @param $path string
     * @return string
     */
    public static function convertPublicToInternal($path)
    {
        if (empty($path)) {
            return $path;
        }
        $urlMap = modstart_config('Site_PublicInternalUrlMap', []);
        if (empty($urlMap) || !is_array($urlMap)) {
            return $path;
        }
        foreach ($urlMap as $urlPair) {
            if (!isset($urlPair['public']) || !isset($urlPair['internal'])) {
                continue;
            }
            $path = str_replace($urlPair['public'], $urlPair['internal'], $path);
        }
        return $path;
    }
}
