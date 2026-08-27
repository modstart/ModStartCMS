<?php

namespace ModStart\Test;

/**
 * 测试 HTTP Server 管理工具 — 统一启动 / 关闭 php artisan serve
 *
 * UI 测试需要真实 HTTP 服务时使用：
 *   TestServer::start();
 *   $url = TestServer::url('/admin/login');
 *   ...
 *   TestServer::stop();   // 测试结束必须关闭，避免内存泄露 / 端口占用
 *
 * server 进程由 TestUi::startServer() 或 seed-test 的 UI 阶段自动管理，
 * 也可在 describe 中手动调用。
 */
class TestServer
{
    /** @var resource|null proc_open 进程句柄 */
    private static $process = null;

    /** @var array|null proc_get_status 信息 */
    private static $status = null;

    /** @var string|null 随机端口 */
    private static $port = null;

    /** @var string|null 启动命令输出管道 */
    private static $pipes = null;

    /**
     * 当前 server 是否已启动
     * @return bool
     */
    public static function isRunning()
    {
        if (empty(self::$process)) {
            return false;
        }
        $status = proc_get_status(self::$process);
        return $status['running'];
    }

    /**
     * 获取当前端口
     * @return int
     */
    public static function port()
    {
        return intval(self::$port);
    }

    /**
     * 获取基础地址
     * @return string
     */
    public static function baseUrl()
    {
        return 'http://127.0.0.1:' . self::$port;
    }

    /**
     * 拼接完整 URL
     * @param string $path 以 / 开头的路径
     * @return string
     */
    public static function url($path)
    {
        if (empty($path)) {
            $path = '/';
        }
        if ($path[0] !== '/') {
            $path = '/' . $path;
        }
        return self::baseUrl() . $path;
    }

    /**
     * 启动 php artisan serve（后台进程），等待端口就绪
     * @param int $port 指定端口，默认随机
     * @return bool 启动成功返回 true
     */
    public static function start($port = 0)
    {
        if (self::isRunning()) {
            return true;
        }
        if ($port <= 0) {
            $port = mt_rand(20000, 30000);
        }
        self::$port = $port;
        $base = base_path();
        $phpBin = defined('PHP_BINARY') ? PHP_BINARY : 'php';
        $cmd = $phpBin . ' ' . escapeshellarg($base . '/artisan') . ' serve --host=127.0.0.1 --port=' . $port;
        $descriptors = [
            0 => ['pipe', 'r'],  // stdin
            1 => ['pipe', 'w'],  // stdout
            2 => ['pipe', 'w'],  // stderr
        ];
        self::$process = proc_open($cmd, $descriptors, self::$pipes, $base);
        if (!is_resource(self::$process)) {
            self::$process = null;
            self::$port = null;
            return false;
        }
        // 等待端口就绪（最长 10 秒）
        $deadline = time() + 10;
        while (time() < $deadline) {
            if (self::portReady($port)) {
                return true;
            }
            usleep(200000);
        }
        self::stop();
        return false;
    }

    /**
     * 关闭 server 进程
     */
    public static function stop()
    {
        if (self::$process) {
            $status = proc_get_status(self::$process);
            if ($status['running']) {
                proc_terminate(self::$process);
                // 等待进程退出
                $deadline = time() + 3;
                while (time() < $deadline) {
                    $s = proc_get_status(self::$process);
                    if (!$s['running']) {
                        break;
                    }
                    usleep(100000);
                }
            }
            if (is_resource(self::$process)) {
                proc_close(self::$process);
            }
        }
        self::$process = null;
        self::$pipes = null;
        self::$status = null;
        self::$port = null;
    }

    /**
     * 检测端口是否已可访问
     * @param int $port
     * @return bool
     */
    private static function portReady($port)
    {
        $sock = @fsockopen('127.0.0.1', $port, $errno, $errstr, 0.5);
        if ($sock) {
            fclose($sock);
            return true;
        }
        return false;
    }
}