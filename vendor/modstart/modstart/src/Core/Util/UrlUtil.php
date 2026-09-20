<?php

namespace ModStart\Core\Util;

use ModStart\Core\Input\Request;

/**
 * @Util URL
 */
class UrlUtil
{
    /**
     * @Util 判断跳转地址是否安全（仅允许站内相对地址或与当前请求域名一致的同源 http/https 地址）
     * @param $url string 待校验的跳转地址
     * @return bool
     */
    public static function isSafeRedirect($url)
    {
        $url = trim((string)$url);
        if ('' === $url) {
            return true;
        }
        // 拒绝控制字符（含 \t\n\r）与反斜杠，浏览器会把 \ 规范化为 /
        if (preg_match('/[\x00-\x1F\x7F]/', $url) || strpos($url, '\\') !== false) {
            return false;
        }
        // 协议相对地址（//host）一律拒绝
        if (strpos($url, '//') === 0) {
            return false;
        }
        $info = @parse_url($url);
        if (false === $info || !is_array($info)) {
            return false;
        }
        $scheme = isset($info['scheme']) ? strtolower($info['scheme']) : '';
        $host = isset($info['host']) ? strtolower($info['host']) : '';
        if ('' !== $scheme) {
            // 带协议时：仅允许 http/https，且必须显式带 host
            // 防止 https:evil.com / http:/evil.com 这类"无 //host"写法被浏览器解析为外站
            if (!in_array($scheme, ['http', 'https'])) {
                return false;
            }
            if ('' === $host) {
                return false;
            }
            // 拒绝 userinfo（https://user@host 形式），避免域名混淆
            if (isset($info['user']) || isset($info['pass'])) {
                return false;
            }
            return $host === self::currentHost();
        }
        // 无协议：解析出 host（如 //host）一律拒绝，其余视为站内相对地址
        if ('' !== $host) {
            return false;
        }
        return true;
    }

    /**
     * 获取当前请求的 host（去掉端口，统一小写）
     * @return string
     */
    public static function currentHost()
    {
        $domain = (string)Request::domain();
        $parsed = @parse_url('http://' . $domain);
        if (is_array($parsed) && !empty($parsed['host'])) {
            return strtolower($parsed['host']);
        }
        return strtolower($domain);
    }
}
