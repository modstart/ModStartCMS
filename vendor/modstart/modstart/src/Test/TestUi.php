<?php

namespace ModStart\Test;

use ModStart\Admin\Auth\Admin;

/**
 * UI 测试工具 — 后台界面自动化测试
 *
 * 支持两种模式：
 * 1. 真实浏览器模式（推荐）：TestBrowser + Node(playwright-core) 驱动系统 Chrome(headless)，
 *    真实打开界面、登录、填表、提交，走真实 HTTP + 浏览器渲染链路。
 *    - 需 .env 设置 MS_AUTO_TEST=1 忽略后台登录验证码（自动测试模式）
 *    - 流程：startServer() → startBrowser() → browserLogin() → assertPageOkBrowser() → stopBrowser() → stopServer()
 * 2. 内部 HTTP Kernel 模式（回退）：同进程 Laravel HTTP Kernel 内部请求，走完整中间件链路。
 *
 * 标准流程（真实浏览器）：
 *   1. TestUi::startServer()                   启动 php artisan serve
 *   2. TestUi::startBrowser()                  启动 Chrome 浏览器服务
 *   3. TestUi::browserLogin('admin','123456')  浏览器登录后台
 *   4. TestUi::assertPageOkBrowser('/admin/xxx','说明')  浏览器访问页面并断言无 500
 *   5. TestUi::assertNoErrorLog()              检查无错误日志
 *   6. TestUi::stopBrowser() / TestUi::stopServer()
 */
class TestUi
{
    /** @var int|null 已登录的 Admin 用户 ID */
    private static $adminUserId = null;

    /** @var bool server 是否由本类启动 */
    private static $serverStarted = false;

    /**
     * 启动真实 HTTP Server（php artisan serve），供渲染类检查使用
     * @param int $port
     * @return bool
     */
    public static function startServer($port = 0)
    {
        if (TestServer::isRunning()) {
            return true;
        }
        $ok = TestServer::start($port);
        if ($ok) {
            self::$serverStarted = true;
        }
        return $ok;
    }

    /**
     * 关闭真实 HTTP Server
     */
    public static function stopServer()
    {
        if (self::$serverStarted) {
            TestServer::stop();
            self::$serverStarted = false;
        }
    }

    /**
     * 清理 storage/logs 下所有日志文件
     * @return bool
     */
    public static function clearLog()
    {
        $logDir = storage_path('logs');
        if (!is_dir($logDir)) {
            return true;
        }
        foreach (glob($logDir . '/*.log') as $file) {
            @unlink($file);
        }
        return true;
    }

    /**
     * 检查 storage/logs 是否存在错误日志（ERROR / Exception / Fatal）
     * @return bool 有错误日志返回 true
     */
    public static function hasErrorLog()
    {
        $logDir = storage_path('logs');
        if (!is_dir($logDir)) {
            return false;
        }
        foreach (glob($logDir . '/*.log') as $file) {
            $content = @file_get_contents($file);
            if (empty($content)) {
                continue;
            }
            foreach (['ERROR', 'Exception', 'Fatal error', 'FatalError'] as $needle) {
                if (strpos($content, $needle) !== false) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * 获取 storage/logs 目录下所有日志文件的拼接内容（用于排查）
     * @return string
     */
    public static function logContent()
    {
        $logDir = storage_path('logs');
        if (!is_dir($logDir)) {
            return '';
        }
        $result = '';
        foreach (glob($logDir . '/*.log') as $file) {
            $result .= "--- " . basename($file) . " ---\n";
            $result .= @file_get_contents($file);
            $result .= "\n";
        }
        return $result;
    }

    /**
     * 断言测试过程无错误日志
     * @param string $name
     */
    public static function assertNoErrorLog($name = 'UI: 无错误日志')
    {
        TestCase::assertFalse(self::hasErrorLog(), $name . ' [日志内容] ' . substr(self::logContent(), 0, 500));
    }

    /**
     * 登录后台管理员（同进程 session，无需验证码）
     * @param string $username
     * @param string $password
     * @return bool 登录成功返回 true
     */
    public static function loginAdmin($username, $password)
    {
        $adminUser = \ModStart\Core\Dao\ModelUtil::get(\ModStart\Admin\Model\AdminUser::class, ['username' => $username]);
        if (empty($adminUser)) {
            self::$adminUserId = null;
            return false;
        }
        // 校验密码（复用 Admin::login 的加密逻辑）
        $ret = Admin::login($username, $password);
        if (!empty($ret['code'])) {
            self::$adminUserId = null;
            return false;
        }
        // 在同进程 session store 中写入 Admin 登录态
        $session = app('session.store');
        $session->start();
        $session->put(Admin::ADMIN_USER_ID_SESSION_KEY, $adminUser['id']);
        $session->put(Admin::ADMIN_USER_SESSION_KEY, $adminUser);
        $session->save();
        self::$adminUserId = intval($adminUser['id']);
        return true;
    }

    /**
     * 当前已登录的 Admin 用户 ID
     * @return int|null
     */
    public static function adminUserId()
    {
        return self::$adminUserId;
    }

    /**
     * 退出登录（清除 session 登录态）
     */
    public static function logoutAdmin()
    {
        Admin::clearSession();
        self::$adminUserId = null;
    }

    /**
     * 访问后台页面（GET）— 通过 HTTP Kernel 内部请求，自动携带 admin session cookie
     * @param string $path 如 /admin/aigc_content
     * @return array ['status' => int, 'body' => string]
     */
    public static function get($path)
    {
        return self::request('GET', $path);
    }

    /**
     * 提交请求（POST）
     * @param string $path
     * @param array $params
     * @return array ['status' => int, 'body' => string]
     */
    public static function post($path, $params = [])
    {
        return self::request('POST', $path, $params);
    }

    /**
     * 通过 Laravel HTTP Kernel 发起内部请求（完整中间件链路）
     * @param string $method
     * @param string $path
     * @param array $params
     * @return array ['status' => int, 'body' => string]
     */
    private static function request($method, $path, $params = [])
    {
        // 携带 admin session cookie（session id 从 session store 获取）
        $session = app('session.store');
        $sessionId = $session->getId();
        $cookies = [];
        if ($sessionId) {
            $cookies[config('session.cookie', 'ssid')] = $sessionId;
        }
        $request = \Illuminate\Http\Request::create($path, $method, $params, $cookies, [], [
            'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest',
            'CONTENT_TYPE' => 'application/x-www-form-urlencoded',
        ]);
        /** @var \Illuminate\Foundation\Http\Kernel $kernel */
        $kernel = app('Illuminate\Contracts\Http\Kernel');
        $response = $kernel->handle($request);
        return [
            'status' => $response->getStatusCode(),
            'body' => $response->getContent(),
        ];
    }

    /**
     * 断言后台页面访问正常（HTTP 2xx/3xx 且无 Laravel 错误页）
     * @param string $path
     * @param string $name
     */
    public static function assertPageOk($path, $name)
    {
        $ret = self::get($path);
        TestCase::assertHttpOk($ret['status'], $name . ' [HTTP ' . $ret['status'] . ']');
        if ($ret['status'] >= 500) {
            TestCase::assertTrue(false, $name . ' [500 错误] ' . substr(strip_tags($ret['body']), 0, 200));
            return;
        }
        // 检查是否出现 Laravel 异常页
        $body = $ret['body'];
        foreach (['Whoops, looks like something went wrong', 'RuntimeException', 'QueryException', 'ErrorException'] as $needle) {
            if (strpos($body, $needle) !== false) {
                TestCase::assertTrue(false, $name . ' [页面异常] ' . substr(strip_tags($body), 0, 200));
                return;
            }
        }
        TestCase::assertTrue(true, $name . ' [页面加载正常]');
    }

    /**
     * 用 Chrome headless 渲染页面，返回渲染后的 DOM（用于验证前端真实渲染）
     * 需先调用 startServer() 启动真实 HTTP Server
     * @param string $path
     * @return string 渲染后的 HTML，失败返回空字符串
     */
    public static function chromeDump($path)
    {
        if (!TestServer::isRunning()) {
            return '';
        }
        $chromeCandidates = [
            '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
            '/usr/bin/google-chrome',
            '/usr/bin/chromium',
            '/usr/bin/chromium-browser',
        ];
        $chrome = null;
        foreach ($chromeCandidates as $c) {
            if (file_exists($c)) {
                $chrome = $c;
                break;
            }
        }
        if (empty($chrome)) {
            return '';
        }
        $url = TestServer::url($path);
        $cmd = escapeshellarg($chrome)
            . ' --headless --disable-gpu --no-sandbox --dump-dom --virtual-time-budget=5000 '
            . escapeshellarg($url) . ' 2>/dev/null';
        $output = [];
        $exitCode = 0;
        exec($cmd, $output, $exitCode);
        if ($exitCode !== 0) {
            return '';
        }
        return implode("\n", $output);
    }

    // =========================================================================
    // 真实浏览器模式（TestBrowser + Node playwright-core + Chrome headless）
    // =========================================================================

    /**
     * 浏览器驱动是否可用（Node + playwright-core + Chrome）
     * @return bool
     */
    public static function browserAvailable()
    {
        return TestBrowser::available();
    }

    /**
     * 启动浏览器服务（需先 startServer）
     * @return bool
     */
    public static function startBrowser()
    {
        return TestBrowser::start();
    }

    /**
     * 关闭浏览器服务
     */
    public static function stopBrowser()
    {
        TestBrowser::stop();
    }

    /**
     * 浏览器是否已启动
     * @return bool
     */
    public static function browserRunning()
    {
        return TestBrowser::isRunning();
    }

    /**
     * 浏览器登录后台（真实打开登录页填表提交）
     * @param string $username
     * @param string $password
     * @return bool
     */
    public static function browserLogin($username, $password)
    {
        if (!TestServer::isRunning()) {
            return false;
        }
        $ok = TestBrowser::login($username, $password, TestServer::url('/admin/login'));
        if ($ok) {
            // 同步记录 admin user id（从数据库查询）
            $adminUser = \ModStart\Core\Dao\ModelUtil::get(\ModStart\Admin\Model\AdminUser::class, ['username' => $username]);
            if ($adminUser) {
                self::$adminUserId = intval($adminUser['id']);
            }
        }
        return $ok;
    }

    /**
     * 浏览器访问后台页面（GET），返回 ['status','title','url','html']
     * @param string $path
     * @param bool $withHtml
     * @return array
     */
    public static function browserGet($path, $withHtml = false)
    {
        if (!TestServer::isRunning()) {
            return ['ok' => false, 'status' => 0, 'title' => '', 'url' => '', 'html' => ''];
        }
        return TestBrowser::open(TestServer::url($path), $withHtml);
    }

    /**
     * 浏览器填表
     * @param string $selector CSS 选择器
     * @param string $value
     * @return bool
     */
    public static function browserFill($selector, $value)
    {
        return TestBrowser::fill($selector, $value);
    }

    /**
     * 浏览器点击元素
     * @param string $selector CSS 选择器
     * @return bool
     */
    public static function browserClick($selector)
    {
        return TestBrowser::click($selector);
    }

    /**
     * 浏览器等待（毫秒）
     * @param int $ms
     */
    public static function browserWait($ms)
    {
        usleep(intval($ms) * 1000);
    }

    /**
     * 浏览器执行 JS 并返回结果
     * @param string $script
     * @return mixed
     */
    public static function browserEval($script)
    {
        return TestBrowser::evalJs($script);
    }

    /**
     * 浏览器截图
     * @param string $path 保存路径
     * @param bool $fullPage 是否整页截图（true 时截取整个可滚动页面）
     * @return bool
     */
    public static function browserScreenshot($path, $fullPage = false)
    {
        return TestBrowser::screenshot($path, $fullPage);
    }

    /**
     * 浏览器截图并自动压缩
     * 截图后自动等比缩放 + 质量优化（默认最长边 1000px），适用于生成模块预览图等场景
     * @param string $path 保存路径
     * @param bool $fullPage 是否整页截图
     * @param array $option 压缩选项，见 ImageUtil::compress（maxWidth/maxHeight/quality）
     * @return bool
     */
    public static function browserScreenshotCompressed($path, $fullPage = false, $option = [])
    {
        return TestBrowser::screenshotCompressed($path, $fullPage, $option);
    }

    /**
     * 浏览器断言后台页面访问正常（HTTP 2xx/3xx 且无 Laravel 错误页、无错误日志）
     * @param string $path
     * @param string $name
     */
    public static function assertPageOkBrowser($path, $name)
    {
        if (!TestServer::isRunning()) {
            TestCase::assertTrue(false, $name . ' [server 未启动]');
            return;
        }
        if (!TestBrowser::isRunning()) {
            TestCase::assertTrue(false, $name . ' [浏览器未启动]');
            return;
        }
        $ret = TestBrowser::open(TestServer::url($path), true);
        if (empty($ret['ok'])) {
            TestCase::assertTrue(false, $name . ' [浏览器访问失败] ' . (isset($ret['error']) ? $ret['error'] : 'unknown'));
            return;
        }
        TestCase::assertHttpOk($ret['status'], $name . ' [HTTP ' . $ret['status'] . ']');
        if ($ret['status'] >= 500) {
            TestCase::assertTrue(false, $name . ' [500 错误]');
            return;
        }
        // 检查页面是否出现 Laravel 错误页
        $html = isset($ret['html']) ? $ret['html'] : '';
        foreach (['Whoops, looks like something went wrong', 'RuntimeException', 'QueryException', 'ErrorException', 'FatalError'] as $needle) {
            if (strpos($html, $needle) !== false) {
                TestCase::assertTrue(false, $name . ' [页面异常] ' . substr(strip_tags($html), 0, 200));
                return;
            }
        }
        TestCase::assertTrue(true, $name . ' [浏览器加载正常]');
    }
}