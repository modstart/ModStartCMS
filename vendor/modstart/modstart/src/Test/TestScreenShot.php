<?php

namespace ModStart\Test;

use ModStart\Core\Util\FileUtil;

/**
 * 模块自动截图工具 — 封装真实浏览器截图 + 自动压缩
 *
 * 功能：
 *   - 自动启动 php artisan serve + 真实浏览器（Chrome headless）并登录后台
 *   - 通过 CDP（Chrome DevTools Protocol）截图，默认浏览器/截图尺寸 1100x800
 *   - 截图后自动压缩（优先 Imagick 系统库，回退 Intervention Image）
 *   - 截图统一存储到 module/<Module>/Temp/ScreenShot/<name>.png
 *     （Temp 目录 git 不跟踪，且 ModuleDeveloper 打包时自动排除）
 *
 * 用法：
 *   // 批量截取后台页面
 *   TestScreenShot::capturePages('AiAutoArticle', [
 *       'task' => '/admin/ai_auto_article/task',
 *       'config' => '/admin/ai_auto_article/config',
 *   ]);
 *   TestScreenShot::stop();
 *
 *   // 单页截图
 *   $file = TestScreenShot::capturePage('AiAutoArticle', '/admin/ai_auto_article/task', 'task');
 *
 * 前置条件：
 *   - .env 设置 MS_AUTO_TEST=1 忽略后台登录验证码（自动测试模式）
 *   - Node + playwright-core + 系统 Chrome 可用；SHOW_BROWSER=1 时显示浏览器界面
 */
class TestScreenShot
{
    /** @var int 默认截图宽度 */
    const DEFAULT_WIDTH = 1100;

    /** @var int 默认截图高度 */
    const DEFAULT_HEIGHT = 800;

    /** @var bool 是否已启动 server + 浏览器 + 登录 */
    private static $started = false;

    /** @var array 已截图文件列表 */
    private static $screenshots = [];

    /**
     * 启动 HTTP Server + 浏览器并登录后台
     * @param string $username
     * @param string $password
     * @return bool
     */
    public static function start($username = 'admin', $password = '123456')
    {
        if (self::$started) {
            return true;
        }
        TestUi::startServer();
        if (!TestUi::startBrowser()) {
            echo "ERROR: 浏览器启动失败（需 Node + playwright-core + 系统 Chrome）\n";
            TestUi::stopServer();
            return false;
        }
        if (!TestUi::browserLogin($username, $password)) {
            echo "ERROR: 后台登录失败（请确认 .env 已设置 MS_AUTO_TEST=1 且 $username/$password 账号存在）\n";
            TestUi::stopBrowser();
            TestUi::stopServer();
            return false;
        }
        self::$started = true;
        return true;
    }

    /**
     * 关闭 Server + 浏览器，输出截图汇总
     */
    public static function stop()
    {
        TestUi::stopBrowser();
        TestUi::stopServer();
        self::$started = false;
        $list = self::$screenshots;
        self::$screenshots = [];
        echo "截图完成，共 " . count($list) . " 张：\n";
        foreach ($list as $file) {
            echo "  $file\n";
        }
        return $list;
    }

    /**
     * 批量截取后台页面（自动压缩），存储到 module/<Module>/Temp/ScreenShot/<name>.png
     * @param string $module 模块标识，如 AiAutoArticle
     * @param array $pages 页面列表 [ name => 后台路由路径 ]
     * @param array $option 截图/压缩选项，见 capturePage
     * @return array 已截图文件列表
     */
    public static function capturePages($module, $pages, $option = [])
    {
        if (!self::$started && !self::start()) {
            return [];
        }
        foreach ($pages as $name => $path) {
            self::capturePage($module, $path, $name, $option);
        }
        return self::$screenshots;
    }

    /**
     * 截取后台页面（自动压缩）
     * 后台页面自动追加 `?_is_tab=1`，仅截图 iframe 内的内容区（不包含后台框架外壳）
     * @param string $module 模块标识，如 AiAutoArticle
     * @param string $path 后台路由路径，如 /admin/ai_auto_article/task
     * @param string $name 截图文件名（不含扩展名），如 task
     * @param array $option [ 'fullPage'=>true, 'maxWidth'=>1100, 'maxHeight'=>800, 'quality'=>80, 'tab'=>true ]
     * @return string|bool 截图文件绝对路径，失败返回 false
     */
    public static function capturePage($module, $path, $name, $option = [])
    {
        if (!self::$started && !self::start()) {
            return false;
        }
        $option = array_merge([
            'fullPage' => true,
            'maxWidth' => self::DEFAULT_WIDTH,
            'maxHeight' => self::DEFAULT_HEIGHT,
            'quality' => 80,
            'tab' => true,
        ], $option);
        if ($option['tab']) {
            // 后台页面仅截图 iframe 内容区：追加 _is_tab=1
            $path = self::withTabQuery($path);
        }
        echo "截图 $path → $name.png ...\n";
        $ret = TestUi::browserGet($path, false);
        if (empty($ret['ok'])) {
            echo "  [跳过] 页面访问失败: " . (isset($ret['error']) ? $ret['error'] : 'unknown') . "\n";
            return false;
        }
        $outDir = base_path('module/' . $module . '/Temp/ScreenShot');
        if (!is_dir($outDir)) {
            @mkdir($outDir, 0755, true);
        }
        if (!is_dir($outDir)) {
            echo "  [跳过] 无法创建截图目录 $outDir\n";
            return false;
        }
        $file = $outDir . '/' . $name . '.png';
        if (!TestUi::browserScreenshotCompressed($file, $option['fullPage'], $option)) {
            echo "  [跳过] 截图失败\n";
            return false;
        }
        self::$screenshots[] = $file;
        echo "  OK $file (" . FileUtil::formatByte(filesize($file)) . ")\n";
        return $file;
    }

    /**
     * 为后台页面路径追加 _is_tab=1 查询参数（仅截图 iframe 内容区）
     * @param string $path
     * @return string
     */
    private static function withTabQuery($path)
    {
        if (strpos($path, '?') === false) {
            return $path . '?_is_tab=1';
        }
        return $path . '&_is_tab=1';
    }
}