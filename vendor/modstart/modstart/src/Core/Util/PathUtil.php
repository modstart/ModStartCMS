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
}
