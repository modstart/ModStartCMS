<?php

namespace ModStart\Core\Assets;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Util\ShellUtil;

class AssetsUtil
{
    public static function cdn()
    {
        return app('assetPathDriver')->getCDN('');
    }

    public static function fix($path, $hash = true)
    {
        if (empty($path)) {
            return $path;
        }
        if (Str::startsWith($path, 'http://') || Str::startsWith($path, 'https://') || Str::startsWith($path, '//')) {
            return $path;
        }
        if (Str::startsWith($path, '/')) {
            $path = substr($path, 1);
        }
        $url = app('assetPathDriver')->getCDN($path);
        if ($hash) {
            $url .= app('assetPathDriver')->getPathWithHash($path);
        } else {
            $url .= $path;
        }
        return $url;
    }

    public static function fixOrDefault($path, $default)
    {
        if (empty($path)) {
            return self::fix($default);
        }
        return self::fix($path);
    }

    public static function url($file)
    {
        return app('assetPathDriver')->getCDN($file) . app('assetPathDriver')->getPathWithHash($file);
    }

    public static function fixCurrentDomain($path)
    {
        if (is_array($path)) {
            return array_values(array_map(function ($p) {
                return self::fixCurrentDomain($p);
            }, $path));
        }
        if (Str::startsWith($path, 'http://') || Str::startsWith($path, 'https://')) {
            return $path;
        }
        if (!Str::startsWith($path, '/')) {
            $path = '/' . $path;
        }
        if (Request::secure()) {
            $schema = 'https';
        } else {
            $schema = 'http';
        }
        if (Str::startsWith($path, '//')) {
            return $schema . ':' . $path;
        }
        return $schema . '://' . Request::server('HTTP_HOST') . $path;
    }

    public static function fixFullFromConfig($configKey)
    {
        $value = modstart_config($configKey);
        if (empty($value)) {
            return null;
        }
        return self::fixFull($value);
    }

    public static function fixFull($path, $hash = true)
    {
        if (is_array($path)) {
            return array_values(array_map(function ($p) use ($hash) {
                return self::fixFull($p, $hash);
            }, $path));
        }
        if (empty($path)) {
            return $path;
        }
        $path = self::fix($path, $hash);
        if (Str::startsWith($path, 'http://') || Str::startsWith($path, 'https://')) {
            return $path;
        }
        if (Request::secure()) {
            $schema = 'https';
        } else {
            $schema = 'http';
        }
        if (Str::startsWith($path, '//')) {
            return $schema . ':' . $path;
        }
        $host = $schema . '://' . Request::server('HTTP_HOST');
        if (ShellUtil::isCli()) {
            $host = config('env.APP_URL');
            if (empty($host)) {
                $host = modstart_config('siteUrl');
            }
        }
        return rtrim($host, '/') . '/' . ltrim($path, '/');
    }

    /**
     * @param $path
     * @param bool $hash
     * @return mixed|string
     * @throws BizException
     * @deprecated delete at 2024-06-12
     */
    public static function fixFullInJob($path, $hash = true)
    {
        if (empty($path)) {
            return $path;
        }
        $path = self::fix($path, $hash);
        if (Str::startsWith($path, 'http://') || Str::startsWith($path, 'https://')) {
            return $path;
        }
        if (Str::startsWith($path, '//')) {
            return 'http:' . $path;
        }
        $domainUrl = config('env.APP_URL');
        BizException::throwsIfEmpty('APP_URL Required In Job', $domainUrl);
        return rtrim($domainUrl, '/') . '/' . ltrim($path, '/');
    }

    /**
     * @param $path
     * @param $cdn
     * @param bool $hash
     * @return string|array
     * @since 1.5.0
     */
    public static function fixFullWithCdn($path, $cdn, $hash = true)
    {
        if (is_array($path)) {
            return array_values(array_map(function ($p) use ($cdn, $hash) {
                return self::fixFullWithCdn($p, $cdn, $hash);
            }, $path));
        }
        if (empty($path)) {
            return $path;
        }
        if (Str::startsWith($path, 'http://') || Str::startsWith($path, 'https://')) {
            return $path;
        }
        return $cdn . ltrim($path, '/');
    }

    public static function fixFullOrDefault($path, $default = null)
    {
        if (is_array($path)) {
            return array_values(array_map(function ($p) use ($default) {
                return self::fixFullOrDefault($p, $default);
            }, $path));
        }
        if (empty($path)) {
            return self::fixFull($default);
        }
        return self::fixFull($path);
    }

    public static function recordsFixFullOrDefault(&$records, $key, $default = null)
    {
        foreach ((array)$key as $kk) {
            foreach ($records as $k => $v) {
                $records[$k][$kk] = self::fixFullOrDefault($v[$kk], $default);
            }
        }
    }

}
