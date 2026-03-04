<?php

namespace ModStart\Core\Util;

use Illuminate\Support\Str;
use ModStart\Core\Input\Request;

/**
 * @Util 路径工具
 */
class PathUtil
{
    /**
     * @Util 判断路径是否为公网地址（http/https/／／ 开头）
     * @param $path string 路径
     * @return bool
     */
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

    /**
     * @Util 修复路径，确保以 / 开头，可选拼接 CDN 前缀
     * @param $path string 路径
     * @param $cdn string|null CDN 前缀
     * @return string
     */
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

    /**
     * @Util 修复路径，路径为空时使用默认路径
     * @param $path string 路径
     * @param $default string 默认路径
     * @param $cdn string|null CDN 前缀
     * @return string
     */
    public static function fixOrDefault($path, $default, $cdn = null)
    {
        if (empty($path)) {
            return self::fix($default, $cdn);
        }
        return self::fix($path, $cdn);
    }

    /**
     * @Util 生成包含协议和域名的完整路径
     * @param $path string 路径
     * @param $cdn string|null CDN 前缀
     * @param $schema string|null 协议（http/https）
     * @return string
     */
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

    /**
     * @Util 生成包含协议和域名的完整路径，路径为空时使用默认路径
     * @param $path string 路径
     * @param $default string 默认路径
     * @param $cdn string|null CDN 前缀
     * @param $schema string|null 协议（http/https）
     * @return string
     */
    public static function fixFullOrDefault($path, $default, $cdn = null, $schema = null)
    {
        if (empty($path)) {
            return self::fixFull($default, $cdn, $schema);
        }
        return self::fixFull($path, $cdn, $schema);
    }

    /**
     * 将外网地址转换为内网地址
     * 在使用第三方存储时，程序拉取外网地址会造成流量浪费，使用此功能可将外网地址映射为内网地址，节省流量
     * 比如将外网地址 http://cdn.example.com/abc.jpg 映射为内网地址 http://cdn.oss-cn-shanghai.aliyuncs.com/abc.jpg
     * 这样程序处理时会自动使用内网地址，而不会造成流量浪费
     * @param $path string
     * @param $option array
     * @return string
     */
    public static function convertPublicToInternal($path, $option = [])
    {
        $option = array_merge([
            'logMatched' => false,
            'logUnmatched' => false,
        ], $option);
        if (empty($path)) {
            return $path;
        }
        $urlMap = modstart_config('Site_PublicInternalUrlMap', []);
        if (empty($urlMap) || !is_array($urlMap)) {
            return $path;
        }
        $pathOld = $path;
        foreach ($urlMap as $urlPair) {
            if (!isset($urlPair['public']) || !isset($urlPair['internal'])) {
                continue;
            }
            $path = str_replace($urlPair['public'], $urlPair['internal'], $path);
        }
        if ($path == $pathOld) {
            if ($option['logUnmatched']) {
                LogUtil::info('PathUtil.ConvertPublicToInternal.Unmatch', [
                    'path' => $path,
                ]);
            }
        } else {
            if ($option['logMatched']) {
                LogUtil::info('PathUtil.ConvertPublicToInternal', [
                    'from' => $pathOld,
                    'to' => $path,
                ]);
            }
        }
        return $path;
    }

    /**
     * @Util 获取 URL 中的文件后缀
     * @param $url string URL 字符串
     * @param $default mixed 无后缀时的默认值
     * @return string|null
     */
    public static function getExtention($url, $default = null)
    {
        $info = parse_url($url);
        if (empty($info['path'])) {
            return null;
        }
        $path = $info['path'];
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        return $ext ? $ext : $default;
    }

    /**
     * @Util 获取 URL 中的文件名（含后缀）
     * @param $url string URL 字符串
     * @return string|null
     */
    public static function getFilename($url)
    {
        $info = parse_url($url);
        if (empty($info['path'])) {
            return null;
        }
        $path = $info['path'];
        $filename = pathinfo($path, PATHINFO_BASENAME);
        return $filename;
    }

    /**
     * @Util 生成一个临时文件路径（按日期/小时/分钟分层目录）
     * @param $ext string 文件后缀
     * @param $prefix string 目录前缀
     * @return string
     */
    public static function temp($ext, $prefix = 'temp')
    {
        return join('/',
            [
                $prefix,
                date('Ymd/H/i'),
                RandomUtil::lowerString(32) . '.' . $ext
            ]
        );
    }
}
