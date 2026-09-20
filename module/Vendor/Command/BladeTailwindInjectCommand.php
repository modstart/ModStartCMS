<?php

namespace Module\Vendor\Command;

use Illuminate\Console\Command;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Util\PlatformUtil;
use Symfony\Component\Process\Process;

/**
 * 扫描 Blade 页面中使用到的 TailwindCSS 工具类，编译出仅包含这些类的 CSS，
 * 写入脚本自动推导出的独立 CSS 文件，并更新 Blade 中的 <link id="pageStyle">。
 *
 * 用法：
 *   php artisan Vendor:BladeTailwindInject module/Vendor/View/pc/report.blade.php
 *   php artisan Vendor:BladeTailwindInject --html a.blade.php --html b.blade.php
 *   php artisan Vendor:BladeTailwindInject --path module/Vendor/View/pc
 *   php artisan Vendor:BladeTailwindInject --html a.blade.php --watch
 *
 * 具体编译逻辑由同目录下的 tailwind/inject-tailwindcss-style.cjs 实现。
 */
class BladeTailwindInjectCommand extends Command
{
    protected $signature = 'Vendor:BladeTailwindInject
        {blade?* : Blade 文件路径（可多个）}
        {--html=* : Blade 文件路径（可多个，等价于位置参数）}
        {--path=* : 目录，递归处理其下所有 *.blade.php}
        {--watch : 监听文件变动，自动重新编译}';

    protected $description = '扫描 Blade 中使用的 TailwindCSS 类，编译并写入对应 CSS 文件，同时更新 <link id="pageStyle">';

    public function handle()
    {
        $files = $this->collectBladeFiles();
        BizException::throwsIf(
            '未找到需要处理的 Blade 文件，请通过位置参数 / --html / --path 指定',
            empty($files)
        );

        $nodeBin = $this->resolveNodeBin();
        BizException::throwsIf(
            '未检测到 Node.js，请先安装 Node.js 后再执行本命令',
            empty($nodeBin)
        );

        $script = __DIR__ . '/tailwind/inject-tailwindcss-style.cjs';
        BizException::throwsIf(
            '样式注入脚本不存在：' . $script,
            !file_exists($script)
        );

        $this->info('Node：' . $nodeBin);
        $this->info('脚本：' . $script);
        $this->info('待处理 Blade 文件：' . count($files) . ' 个');
        foreach ($files as $file) {
            $this->line('  - ' . $file);
        }

        $process = new Process($this->buildCommand($nodeBin, $script, $files));
        $process->setTimeout(null);
        // 脚本日志走 stderr，需要实时回显，避免管道缓冲区写满导致子进程阻塞
        $process->run(function ($type, $buffer) {
            $this->output->write($buffer);
        });

        BizException::throwsIf(
            '样式注入失败（退出码 ' . $process->getExitCode() . '），请查看上方日志',
            !$process->isSuccessful()
        );

        $this->info('完成：共处理 ' . count($files) . ' 个 Blade 文件，CSS 已写入脚本推导的对应文件');
        return 0;
    }

    /**
     * 收集去重后的 Blade 文件绝对路径
     * @return array
     */
    private function collectBladeFiles()
    {
        $files = array();

        $inputs = array_merge(
            $this->toArray($this->argument('blade')),
            $this->toArray($this->option('html'))
        );
        foreach ($inputs as $item) {
            if (!is_string($item) || trim($item) === '') {
                continue;
            }
            $file = $this->normalizePath($item);
            BizException::throwsIf('Blade 文件不存在：' . $item, !is_file($file));
            $files[$file] = $file;
        }

        foreach ($this->toArray($this->option('path')) as $item) {
            if (!is_string($item) || trim($item) === '') {
                continue;
            }
            $dir = $this->normalizePath($item);
            BizException::throwsIf('目录不存在：' . $item, !is_dir($dir));
            $found = array();
            $this->scanBladeFiles($dir, $found);
            foreach ($found as $file) {
                $files[$file] = $file;
            }
        }

        return array_values($files);
    }

    /**
     * 递归收集目录下的 *.blade.php
     * @param string $dir
     * @param array $result
     */
    private function scanBladeFiles($dir, &$result)
    {
        $items = @scandir($dir);
        if ($items === false) {
            return;
        }
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $full = rtrim($dir, '/') . '/' . $item;
            if (is_dir($full)) {
                $this->scanBladeFiles($full, $result);
            } elseif (is_file($full)) {
                if (substr($full, -10) === '.blade.php') {
                    $result[] = $full;
                }
            }
        }
    }

    /**
     * 相对路径基于项目根目录转绝对路径
     * @param string $path
     * @return string
     */
    private function normalizePath($path)
    {
        $path = str_replace('\\', '/', trim($path));
        if (strpos($path, '/') === 0 || preg_match('#^[A-Za-z]:/#', $path)) {
            return rtrim($path, '/');
        }
        return rtrim(base_path(), '/') . '/' . ltrim($path, '/');
    }

    /**
     * 定位 node 可执行文件
     * @return string|null
     */
    private function resolveNodeBin()
    {
        if (PlatformUtil::isWindows()) {
            $output = @shell_exec('where node');
        } else {
            $output = @shell_exec('command -v node 2>/dev/null');
        }
        $output = trim((string)$output);
        if ($output === '') {
            return null;
        }
        foreach (preg_split('/\r\n|\r|\n/', $output) as $line) {
            $line = trim($line);
            if ($line !== '') {
                return $line;
            }
        }
        return null;
    }

    /**
     * 组装 node 命令（Symfony Process 2.x 需要字符串命令，逐个转义）
     * @param string $nodeBin
     * @param string $script
     * @param array $files
     * @return string
     */
    private function buildCommand($nodeBin, $script, $files)
    {
        $parts = array(escapeshellarg($nodeBin), escapeshellarg($script));
        foreach ($files as $file) {
            $parts[] = '--html';
            $parts[] = escapeshellarg($file);
        }
        if ($this->option('watch')) {
            $parts[] = '--watch';
        }
        return implode(' ', $parts);
    }

    /**
     * 统一数组化（兼容未定义时返回 null/字符串的情况）
     * @param mixed $value
     * @return array
     */
    private function toArray($value)
    {
        if (is_array($value)) {
            return $value;
        }
        if ($value === null || $value === '') {
            return array();
        }
        return array($value);
    }
}
