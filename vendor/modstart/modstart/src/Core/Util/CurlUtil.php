<?php

namespace ModStart\Core\Util;

use Illuminate\Support\Str;
use ModStart\Core\Exception\BizException;

/**
 * @Util HTTP 请求工具
 */
class CurlUtil
{
    private static function JSONResult($code, $msg = '', $data = null)
    {
        $result = [
            'code' => $code,
            'msg' => $msg,
        ];
        if (null !== $data) {
            $result['data'] = $data;
        }
        return $result;
    }

    private static function removeStringBOF($str)
    {
        if (strlen($str) >= 3) {
            if ('EFBBBF' == sprintf('%X%X%X', ord($str[0]), ord($str[1]), ord($str[2]))) {
                return substr($str, 3);
            }
        }
        return $str;
    }

    /**
     * @Util 发送 GET 请求并返回 JSON 中的 data 字段
     * @param $url string 请求 URL
     * @param $param array 请求参数
     * @param $option array 选项
     * @return mixed|null
     */
    public static function getJSONData($url, $param = [], $option = [])
    {
        $ret = self::getJSON($url, $param, $option);
        if ($ret['code'] === 0) {
            return $ret['data'];
        }
        return null;
    }

    /**
     * @Util 发送 GET 请求并解析 JSON 响应体
     * @param $url string 请求 URL
     * @param $param array 请求参数
     * @param $option array 选项
     * @return array code/msg/data
     */
    public static function getJSON($url, $param = [], $option = [])
    {
        $result = self::get($url, $param, $option);
        if (empty($result['body'])) {
            return self::JSONResult(-1, 'CurlUtil.getJSON result empty');
        }
        $result['body'] = self::removeStringBOF($result['body']);
        $json = @json_decode($result['body'], true);
        if (empty($json)) {
            return self::JSONResult(-1, 'CurlUtil.getJSON json parse error', $result['body']);
        }
        return self::JSONResult(0, '', $json);
    }

    /**
     * @Util 发送 POST 请求并返回响应内容字符串
     * @param $url string 请求 URL
     * @param $param array 请求参数
     * @param $option array 选项
     * @return string|null
     */
    public static function postContent($url, $param = [], $option = [])
    {
        $result = self::post($url, $param, $option);
        if (empty($result['body'])) {
            return null;
        }
        return $result['body'];
    }

    /**
     * @Util 发送 POST 请求并返回 JSON 中的 data 字段（失败返回包含 code/msg 的数组）
     * @param $url string 请求 URL
     * @param $param array 请求参数
     * @param $option array 选项
     * @return mixed
     */
    public static function postJSONBody($url, $param = [], $option = [])
    {
        $ret = self::postJSON($url, $param, $option);
        if ($ret['code']) {
            return $ret;
        }
        return $ret['data'];
    }

    /**
     * @Util 发送 POST 请求并解析 JSON 响应体
     * @param $url string 请求 URL
     * @param $param array 请求参数
     * @param $option array 选项
     * @return array code/msg/data
     */
    public static function postJSON($url, $param = [], $option = [])
    {
        $result = self::post($url, $param, $option);
        if (empty($result['body'])) {
            LogUtil::info('CurlUtil.postJSON.Empty', $result);
            return self::JSONResult(-1, 'CurlUtil.postJSON result empty');
        }
        $result['body'] = self::removeStringBOF($result['body']);
        $json = @json_decode($result['body'], true);
        if (empty($json)) {
            return self::JSONResult(-1, 'CurlUtil.postJSON json parse error', $result['body']);
        }
        return self::JSONResult(0, '', $json);
    }

    /**
     * @Util 发送 PUT 请求并解析 JSON 响应体
     * @param $url string 请求 URL
     * @param $param array 请求参数
     * @param $option array 选项
     * @return array code/msg/data
     */
    public static function putJSON($url, $param = [], $option = [])
    {
        $result = self::put($url, $param, $option);
        if (empty($result['body'])) {
            return self::JSONResult(-1, 'CurlUtil.putJSON result empty');
        }
        $result['body'] = self::removeStringBOF($result['body']);
        $json = @json_decode($result['body'], true);
        if (empty($json)) {
            return self::JSONResult(-1, 'CurlUtil.putJSON json parse error', $result['body']);
        }
        return self::JSONResult(0, '', $json);
    }

    /**
     * @Util 发送 DELETE 请求并解析 JSON 响应体
     * @param $url string 请求 URL
     * @param $param array 请求参数
     * @param $option array 选项
     * @return array code/msg/data
     */
    public static function deleteJSON($url, $param = [], $option = [])
    {
        $result = self::delete($url, $param, $option);
        if (empty($result['body'])) {
            return self::JSONResult(-1, 'CurlUtil.deleteJSON result empty');
        }
        $result['body'] = self::removeStringBOF($result['body']);
        $json = @json_decode($result['body'], true);
        if (empty($json)) {
            return self::JSONResult(-1, 'CurlUtil.deleteJSON json parse error', $result['body']);
        }
        return self::JSONResult(0, '', $json);
    }

    /**
     * @Util 发送 GET 请求并返回原始结果
     * @param $url string 请求 URL
     * @param $param array 请求参数
     * @param $option array 选项
     * @return array
     */
    public static function get($url, $param = [], $option = [])
    {
        $option['method'] = 'get';
        return self::request($url, $param, $option);
    }

    /**
     * @Util 发送 POST 请求并返回原始结果
     * @param $url string 请求 URL
     * @param $param array 请求参数
     * @param $option array 选项
     * @return array
     */
    public static function post($url, $param = [], $option = [])
    {
        $option['method'] = 'post';
        return self::request($url, $param, $option);
    }

    /**
     * @Util 发送 PUT 请求并返回原始结果
     * @param $url string 请求 URL
     * @param $param array 请求参数
     * @param $option array 选项
     * @return array
     */
    public static function put($url, $param = [], $option = [])
    {
        $option['method'] = 'put';
        return self::request($url, $param, $option);
    }

    /**
     * @Util 发送 DELETE 请求并返回原始结果
     * @param $url string 请求 URL
     * @param $param array 请求参数
     * @param $option array 选项
     * @return array
     */
    public static function delete($url, $param = [], $option = [])
    {
        $option['method'] = 'delete';
        return self::request($url, $param, $option);
    }

    private static $requestEnd = null;
    private static $requestParam = [];

    /**
     * @Util 通用 HTTP 请求入口，支持 GET/POST/PUT/DELETE 、请求头、代理、超时等
     * @param $url string 请求 URL
     * @param $param array 请求参数
     * @param $option array 选项
     * @return array
     */
    public static function request($url, $param, $option = [])
    {
        $sendHeaders = [];
        if (!empty($option['header'])) {
            foreach ($option['header'] as $k => $v) {
                BizException::throwsIf('CurlUtil.request - header key is numeric', is_numeric($k));
                $sendHeaders[] = "$k:$v";
            }
        }
        $returnHeader = false;
        if (!empty($option['returnHeader'])) {
            $returnHeader = true;
        }
        if (!isset($option['method'])) {
            $option['method'] = 'get';
        }
        if (!isset($option['timeout'])) {
            $option['timeout'] = 30;
        }
        $option['method'] = strtolower($option['method']);

        $result = [];
        $result['code'] = 0;
        $result['body'] = null;
        if ($returnHeader) {
            $result['header'] = [];
            $result['headerMap'] = [];
        }
        if (!empty($option['query'])) {
            $split = Str::contains($url, '?') ? '&' : '?';
            $url = $url . $split . http_build_query($option['query']);
        }
        switch ($option['method']) {
            case 'get':
                if (!empty($param)) {
                    $split = Str::contains($url, '?') ? '&' : '?';
                    $url = $url . $split . http_build_query($param);
                }
                break;
        }
        $ch = curl_init($url);

        if (!empty($option['debugFile'])) {
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            $fp = fopen($option['debugFile'], 'w');
            curl_setopt($ch, CURLOPT_STDERR, $fp);
        }

        curl_setopt($ch, CURLOPT_HEADER, $returnHeader);
        if (!empty($sendHeaders)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $sendHeaders);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $option['timeout']);
        if (defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')) {
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        }
        switch ($option['method']) {
            case 'post':
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
                break;
            case 'put':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
                break;
            case 'delete':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
                break;
            case 'get':
                // ignore
                break;
        }
        if (strpos($url, 'https://') === 0) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        if (!empty($option['socks5'])) {
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
            curl_setopt($ch, CURLOPT_PROXY, $option['socks5']);
        } else if (!empty($option['http_proxy'])) {
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
            curl_setopt($ch, CURLOPT_PROXY, $option['http_proxy']);
        }
        if (!isset($option['userAgent'])) {
            $option['userAgent'] = self::defaultUserAgent();
        }
        if (isset($option['userAgentAppend'])) {
            $option['userAgent'] .= ' ' . $option['userAgentAppend'];
        }
        if (!empty($option['userAgent'])) {
            curl_setopt($ch, CURLOPT_USERAGENT, $option['userAgent']);
        }

        if (!empty($option['writeFunctionCallback'])) {
            curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($curl, $data) use (&$option) {
                call_user_func_array($option['writeFunctionCallback'], [$data, []]);
                return strlen($data);
            });
        }
        if (self::$requestEnd === null) {
            register_shutdown_function(function () {
                if (!self::$requestEnd) {
                    LogUtil::error('CurlUtil.request.timeout', static::$requestParam);
                }
            });
        }
        self::$requestEnd = false;
        self::$requestParam = [
            'url' => $url,
            'param' => $param,
            'option' => $option,
        ];
        $output = curl_exec($ch);
        self::$requestEnd = true;
        if (!empty($option['debugFile'])) {
            file_put_contents($option['debugFile'], "\n\n" . $output, FILE_APPEND);
        }
        $result['code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($returnHeader) {
            $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $headerString = substr($output, 0, $headerSize);
            $result['body'] = substr($output, $headerSize);
            foreach (explode("\n", $headerString) as $line) {
                $line = trim($line);
                if (preg_match('/^(.*?):(.*?)$/', $line, $mat)) {
                    $result['header'] [] = [
                        trim($mat[1]) => trim($mat[2])
                    ];
                    $result['headerMap'][trim($mat[1])] = trim($mat[2]);
                }
            }
        } else {
            $result['body'] = $output;
        }
        if (0 === $result['code']) {
            $result['error'] = curl_error($ch);
        }
        curl_close($ch);
        if ($result['body'] && bin2hex(substr($result['body'], 0, 2)) === '1f8b') {
            $result['body'] = gzdecode($result['body']);
        }
        return $result;
    }

    /**
     * @Util 通过中转代理发送请求
     * @param $proxy string 代理 URL
     * @param $url string 目标 URL
     * @param $param array 请求参数
     * @param $option array 选项
     * @return mixed
     */
    public static function proxyRequest($proxy, $url, $param = [], $option = [])
    {
        $package = $option;
        $package['url'] = $url;
        $package['param'] = $param;
        $url = "$proxy?package=" . urlencode(base64_encode(SerializeUtil::jsonEncode($package)));
        $content = self::getRaw($url);
        $content = @base64_decode($content);
        $content = @unserialize($content);
        return $content;
    }

    /**
     * @Util 通过中转代理发送自定义数据包
     * @param $proxy string 代理 URL
     * @param $package array 请求数据包
     * @return mixed
     */
    public static function proxyCommon($proxy, $package)
    {
        $url = "$proxy?package=" . urlencode(base64_encode(SerializeUtil::jsonEncode($package)));
        $content = self::getRaw($url);
        $content = @base64_decode($content);
        $content = @unserialize($content);
        return $content;
    }

    /**
     * @Util 发送 POST 请求并返回响应内容字符串（简化版）
     * @param $url string 请求 URL
     * @param $param array 请求参数
     * @param $option array 选项
     * @return string|null
     */
    public static function postRaw($url, $param = [], $option = [])
    {
        if (empty($option['timeout'])) {
            $option['timeout'] = 30;
        }
        $sendHeaders = [];
        if (!empty($option['header'])) {
            foreach ($option['header'] as $k => $v) {
                $sendHeaders[] = "$k:$v";
            }
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        if (!empty($sendHeaders)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $sendHeaders);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $option['timeout']);
        if (!empty($option['referer'])) {
            curl_setopt($ch, CURLOPT_REFERER, $option['referer']);
        }
        if (defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')) {
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        }
        if (StrUtil::startWith($url, 'https://')) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        if (!isset($option['userAgent'])) {
            $option['userAgent'] = self::defaultUserAgent();
        }
        if (!empty($option['userAgent'])) {
            curl_setopt($ch, CURLOPT_USERAGENT, $option['userAgent']);
        }
        $temp = curl_exec($ch);
        curl_close($ch);
        return $temp;
    }

    /**
     * @Util 发送 GET 请求并返回响应内容字符串（简化版）
     * @param $url string 请求 URL
     * @param $param array 请求参数
     * @param $option array 选项（returnRaw=true 时返回原始信息数组）
     * @return string|null|array
     */
    public static function getRaw($url, $param = [], $option = [])
    {
        if (empty($option['timeout'])) {
            $option['timeout'] = 30;
        }
        if (!empty($param)) {
            $url = $url . '?' . http_build_query($param);
        }
        $sendHeaders = [];
        if (!empty($option['header'])) {
            foreach ($option['header'] as $k => $v) {
                $sendHeaders[] = "$k:$v";
            }
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        if (!empty($sendHeaders)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $sendHeaders);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $option['timeout']);
        if (!empty($option['referer'])) {
            curl_setopt($ch, CURLOPT_REFERER, $option['referer']);
        }
        if (defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')) {
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        }
        if (StrUtil::startWith($url, 'https://')) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        if (!isset($option['userAgent'])) {
            if (!empty($option['mockUserAgent'])) {
                $option['userAgent'] = self::mockUserAgent();
            } else {
                $option['userAgent'] = self::defaultUserAgent();
            }
        }
        if (!empty($option['userAgent'])) {
            curl_setopt($ch, CURLOPT_USERAGENT, $option['userAgent']);
        }
        $temp = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        $error = curl_error($ch);
        curl_close($ch);
        if (!empty($option['returnRaw'])) {
            $ext = FileUtil::mimeToExt($contentType);
            return [
                'httpCode' => $httpCode,
                'contentType' => $contentType,
                'ext' => $ext,
                'body' => $temp,
                'error' => $error,
            ];
        }
        if (200 != $httpCode) {
            return null;
        }
        return $temp;
    }

    /**
     * @Util 获取默认 User-Agent 字符串
     * @return string
     */
    public static function defaultUserAgent()
    {
        $userAgent = 'MSCore/' . modstart_version() . ' PHP/' . PHP_VERSION . ' OS/' . PHP_OS;
        $appInfo = [];
        if (class_exists(\App\Constant\AppConstant::class)) {
            if (defined('\\App\\Constant\\AppConstant::APP')) {
                $appInfo[] = strtoupper(\App\Constant\AppConstant::APP);
            }
            if (defined('\\App\\Constant\\AppConstant::VERSION')) {
                $appInfo[] = \App\Constant\AppConstant::VERSION;
            }
        }
        if (!empty($appInfo)) {
            $userAgent = implode('/', $appInfo) . ' ' . $userAgent;
        }
        return $userAgent;
    }

    /**
     * @Util 获取模拟浏览器的 User-Agent 字符串
     * @return string
     */
    public static function mockUserAgent()
    {
        return 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36';
    }
}
