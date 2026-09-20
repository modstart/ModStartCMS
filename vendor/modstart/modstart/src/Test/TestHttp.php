<?php

namespace ModStart\Test;

/**
 * HTTP 测试工具 — 通过 Laravel HTTP Kernel 发起内部请求，测试完整路由+中间件链路
 *
 * 基本用法：
 *   $ret = TestHttp::post('/api/banner/get');
 *   TestCase::assertSuccess($ret, 'Banner API');
 *
 * 带 Member 认证：
 *   $token = \Module\Member\Test\Support\TestMember::loginAsTestHttp();
 *   TestHttp::useToken($token);
 *   $ret = TestHttp::post('/api/member/current');
 *   TestHttp::clearToken();
 *   TestCase::assertSuccess($ret, 'Member API');
 */
class TestHttp
{
    /** @var string|null 当前 api-token（= session id） */
    private static $apiToken = null;

    /**
     * 设置 api-token（用于 Member 认证）
     * @param string|null $token
     */
    public static function useToken($token)
    {
        self::$apiToken = $token;
    }

    /**
     * 清除 api-token
     */
    public static function clearToken()
    {
        self::$apiToken = null;
    }

    /**
     * 发起 POST 请求
     * @param string $path  完整路径，如 /api/banner/get
     * @param array  $params POST 参数
     * @return array  解析后的 JSON 响应，失败时返回 ['code' => -1, 'msg' => '...']
     */
    public static function post($path, $params = [])
    {
        return self::request('POST', $path, $params);
    }

    /**
     * 发起 GET 请求
     * @param string $path
     * @param array  $params Query 参数
     * @return array
     */
    public static function get($path, $params = [])
    {
        return self::request('GET', $path, $params);
    }

    /**
     * 发起带文件的 POST 请求（multipart/form-data）
     * @param string $path      完整路径
     * @param array  $params    表单参数
     * @param string $fileField 文件字段名
     * @param string $filePath  本地文件绝对路径
     * @return array
     */
    public static function postFile($path, $params = [], $fileField = 'file', $filePath = '')
    {
        $file = new \Symfony\Component\HttpFoundation\File\UploadedFile($filePath, basename($filePath), null, null, null, true);
        return self::request('POST', $path, $params, [$fileField => $file]);
    }

    /**
     * 执行请求并返回原始响应对象（用于校验状态码、响应头等）
     * @param string $method
     * @param string $path
     * @param array  $params
     * @param array  $files
     * @param bool   $ajax   是否带 X-Requested-With 头（默认 true，设为 false 可用于测试跳转等非 ajax 响应）
     * @return \Illuminate\Http\Response
     */
    public static function response($method, $path, $params = [], $files = [], $ajax = true)
    {
        // 将 api-token 注入请求参数，SessionMiddleware 会根据它恢复 Session
        if (self::$apiToken) {
            $params['api-token'] = self::$apiToken;
        }

        $server = [
            'CONTENT_TYPE' => empty($files) ? 'application/x-www-form-urlencoded' : 'multipart/form-data',
        ];
        if ($ajax) {
            $server['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        }

        $request = \Illuminate\Http\Request::create($path, $method, $params, [], $files, $server);

        /** @var \Illuminate\Foundation\Http\Kernel $kernel */
        $kernel = app('Illuminate\Contracts\Http\Kernel');
        return $kernel->handle($request);
    }

    /**
     * 执行请求并返回解析后的响应数据
     * @param string $method
     * @param string $path
     * @param array  $params
     * @param array  $files
     * @return array
     */
    private static function request($method, $path, $params = [], $files = [])
    {
        $response = self::response($method, $path, $params, $files);

        $statusCode = $response->getStatusCode();
        $content    = $response->getContent();

        // 非 200 响应 — 提取简短错误说明
        if ($statusCode !== 200) {
            $short = substr(strip_tags($content), 0, 200);
            return ['code' => -1, 'msg' => "HTTP $statusCode: $short"];
        }

        $data = json_decode($content, true);
        if ($data === null) {
            return ['code' => -1, 'msg' => 'Invalid JSON: ' . substr($content, 0, 200)];
        }
        return $data;
    }
}
