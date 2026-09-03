<?php

namespace ModStart\Test;

use ModStart\Core\Util\ImageUtil;

/**
 * 真实浏览器测试驱动 — 通过 Node + Playwright(playwright-core) 驱动系统 Chrome(headless)
 *
 * 启动一个常驻 Node 浏览器服务进程（browser-server.js），通过 stdin/stdout 行 JSON 通信：
 *   - 登录后台（真实打开登录页、填表、提交）
 *   - 访问页面（携带登录态 cookie）
 *   - 填表 / 点击 / 执行 JS / 截图
 *
 * 依赖：
 *   - Node.js（node 命令可用）
 *   - playwright-core（项目根 node_modules 或本目录 node_modules）
 *   - 系统 Chrome / Chromium
 *
 * 使用：
 *   TestBrowser::start();
 *   TestBrowser::login('admin', '123456', 'http://127.0.0.1:xxxx/admin/login');
 *   $ret = TestBrowser::goto('http://127.0.0.1:xxxx/admin/ai_auto_article/task');
 *   TestBrowser::stop();
 */
class TestBrowser
{
    /** @var resource|null proc_open 进程句柄 */
    private static $process = null;

    /** @var array|null 管道 */
    private static $pipes = null;

    /** @var string|null 脚本路径 */
    private static $scriptPath = null;

    /** @var bool 是否已启动 */
    private static $started = false;

    /**
     * 浏览器服务脚本路径
     * @return string
     */
    private static function scriptPath()
    {
        if (null === self::$scriptPath) {
            self::$scriptPath = __DIR__ . '/browser/browser-server.js';
        }
        return self::$scriptPath;
    }

    /**
     * 检测浏览器驱动是否可用（Node + 脚本存在）
     * @return bool
     */
    public static function available()
    {
        if (!file_exists(self::scriptPath())) {
            return false;
        }
        $nodeBin = trim(shell_exec('command -v node 2>/dev/null'));
        return !empty($nodeBin);
    }

    /**
     * 是否已启动
     * @return bool
     */
    public static function isRunning()
    {
        if (!self::$process || !self::$started) {
            return false;
        }
        $status = proc_get_status(self::$process);
        return $status['running'];
    }

    /**
     * 启动浏览器服务进程，等待就绪
     * @return bool
     */
    public static function start()
    {
        if (self::isRunning()) {
            return true;
        }
        if (!self::available()) {
            return false;
        }
        // SHOW_BROWSER=1 时显示浏览器界面（headed），默认 headless 不显示
        $showBrowser = config('env.SHOW_BROWSER', false);
        if (!$showBrowser) {
            $showBrowser = getenv('SHOW_BROWSER');
        }
        putenv('SHOW_BROWSER=' . ($showBrowser ? '1' : '0'));
        $cmd = 'node ' . escapeshellarg(self::scriptPath());
        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];
        self::$process = proc_open($cmd, $descriptors, self::$pipes, base_path());
        if (!is_resource(self::$process)) {
            self::$process = null;
            return false;
        }
        self::$started = true;
        // 等待浏览器就绪：发送 ping 指令（goto about:blank）
        $deadline = time() + 20;
        while (time() < $deadline) {
            if (!self::isRunning()) {
                return false;
            }
            $ret = self::command('goto', ['url' => 'about:blank']);
            if (!empty($ret) && isset($ret['ok'])) {
                return true;
            }
            usleep(300000);
        }
        self::stop();
        return false;
    }

    /**
     * 关闭浏览器服务进程
     */
    public static function stop()
    {
        if (self::processRunning()) {
            try {
                self::writeCommand(['action' => 'close']);
                // 等待进程退出
                $deadline = time() + 5;
                while (time() < $deadline && self::processRunning()) {
                    usleep(100000);
                }
            } catch (\Exception $e) {
                // ignore
            }
        }
        if (is_resource(self::$process)) {
            proc_terminate(self::$process);
            $deadline = time() + 3;
            while (time() < $deadline && self::processRunning()) {
                usleep(100000);
            }
            proc_close(self::$process);
        }
        self::$process = null;
        self::$pipes = null;
        self::$started = false;
    }

    /**
     * 进程是否存活
     * @return bool
     */
    private static function processRunning()
    {
        if (!self::$process) {
            return false;
        }
        $status = proc_get_status(self::$process);
        return $status['running'];
    }

    /**
     * 发送指令并读取结果（串行同步）
     * @param string $action
     * @param array $params
     * @return array|null 结果数组，失败返回 null
     */
    public static function command($action, $params = [])
    {
        if (!self::isRunning()) {
            return null;
        }
        $params['action'] = $action;
        if (!self::writeCommand($params)) {
            return null;
        }
        return self::readResult();
    }

    /**
     * 写入一行 JSON 指令
     * @param array $params
     * @return bool
     */
    private static function writeCommand($params)
    {
        if (empty(self::$pipes) || empty(self::$pipes[0])) {
            return false;
        }
        $line = json_encode($params) . "\n";
        $written = fwrite(self::$pipes[0], $line);
        fflush(self::$pipes[0]);
        return ($written !== false && $written > 0);
    }

    /**
     * 从 stdout 读取一行 JSON 结果（带超时）
     * @return array|null
     */
    private static function readResult()
    {
        if (empty(self::$pipes) || empty(self::$pipes[1])) {
            return null;
        }
        $read = [self::$pipes[1]];
        $write = null;
        $except = null;
        $ready = @stream_select($read, $write, $except, 60, 0);
        if ($ready === false || $ready === 0) {
            return null;
        }
        $line = fgets(self::$pipes[1]);
        if ($line === false) {
            return null;
        }
        $data = json_decode(trim($line), true);
        if (!is_array($data)) {
            return null;
        }
        return $data;
    }

    /**
     * 浏览器登录后台
     * @param string $username
     * @param string $password
     * @param string $loginUrl 登录页完整 URL
     * @return bool
     */
    public static function login($username, $password, $loginUrl)
    {
        $ret = self::command('login', [
            'url' => $loginUrl,
            'username' => $username,
            'password' => $password,
        ]);
        if (empty($ret) || empty($ret['ok'])) {
            return false;
        }
        return !empty($ret['loggedIn']);
    }

    /**
     * 打开页面
     * @param string $url
     * @param bool $withHtml 是否返回 HTML
     * @return array ['ok','status','title','url','html']
     */
    public static function open($url, $withHtml = false)
    {
        $ret = self::command('goto', [
            'url' => $url,
            'withHtml' => $withHtml,
        ]);
        if (empty($ret) || empty($ret['ok'])) {
            return [
                'ok' => false,
                'status' => 0,
                'title' => '',
                'url' => '',
                'html' => '',
                'error' => isset($ret['error']) ? $ret['error'] : 'browser command failed',
            ];
        }
        return $ret;
    }

    /**
     * 填表
     * @param string $selector
     * @param string $value
     * @return bool
     */
    public static function fill($selector, $value)
    {
        $ret = self::command('fill', ['selector' => $selector, 'value' => $value]);
        return !empty($ret) && !empty($ret['ok']);
    }

    /**
     * 点击元素
     * @param string $selector
     * @return bool
     */
    public static function click($selector)
    {
        $ret = self::command('click', ['selector' => $selector]);
        return !empty($ret) && !empty($ret['ok']);
    }

    /**
     * 执行 JS 并返回结果
     * @param string $script
     * @return mixed
     */
    public static function evalJs($script)
    {
        $ret = self::command('eval', ['script' => $script]);
        if (empty($ret) || empty($ret['ok'])) {
            return null;
        }
        return isset($ret['data']) ? $ret['data'] : null;
    }

    /**
     * 获取当前页面 HTML
     * @return string
     */
    public static function html()
    {
        $ret = self::command('html');
        if (empty($ret) || empty($ret['ok'])) {
            return '';
        }
        return isset($ret['html']) ? $ret['html'] : '';
    }

    /**
     * 截图
     * @param string $path 保存路径
     * @param bool $fullPage 是否整页截图（true 时截取整个可滚动页面）
     * @return bool
     */
    public static function screenshot($path, $fullPage = false)
    {
        $ret = self::command('screenshot', ['path' => $path, 'fullPage' => $fullPage]);
        return !empty($ret) && !empty($ret['ok']);
    }

    /**
     * 截图并自动压缩
     * 截图后通过 ImageUtil::compress 自动等比缩放 + 质量优化，避免图片过大
     * @param string $path 保存路径
     * @param bool $fullPage 是否整页截图
     * @param array $option 压缩选项，见 ImageUtil::compress（maxWidth/maxHeight/quality）
     * @return bool
     */
    public static function screenshotCompressed($path, $fullPage = false, $option = [])
    {
        if (!self::screenshot($path, $fullPage)) {
            return false;
        }
        if (!file_exists($path)) {
            return false;
        }
        return ImageUtil::compress($path, $option);
    }
}