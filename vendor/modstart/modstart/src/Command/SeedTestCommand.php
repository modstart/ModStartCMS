<?php

namespace ModStart\Command;

use Illuminate\Console\Command;
use ModStart\Module\ModuleManager;
use ModStart\Test\TestContext;

class SeedTestCommand extends Command
{
    use SeedTestTrait;

    protected $signature = 'modstart:seed-test';
    protected $description = '执行系统自动化测试（Seed 填充 + API 测试 + Biz 测试）';

    public function handle()
    {
        if (!$this->checkTestEnvironment()) {
            return 1;
        }

        TestContext::reset();

        $this->info('');
        $this->info('=== modstart:seed-test ===');
        $this->info('');

        // Step 1: 删除所有数据库表
        $this->comment('[ Step 1 ] 删除所有数据库表');
        if (!$this->dropAllTables()) {
            return 1;
        }

        // Step 1.5: 校验修改过的迁移文件类名（类名错误会在 migrate 时崩溃，需前置校验）
        $this->comment('[ Step 1.5 ] 校验迁移文件类名');
        if (!$this->checkMigrationClassNames()) {
            return 1;
        }

        // Step 2: 运行数据库迁移
        $this->comment('[ Step 2 ] 运行 migrate');
        if (!$this->runMigrate()) {
            return 1;
        }

        // Step 3: 安装所有模块（部分模块可能有非致命错误，不中断）
        $this->comment('[ Step 3 ] 运行 modstart:module-install-all');
        $this->installAllModules();

        // Step 4: 初始化默认超级管理员（admin / 123456）
        $this->comment('[ Step 4 ] 初始化默认超级管理员');
        $this->initDefaultAdmin();

        // 获取所有已启用的模块名列表
        $enabledModules = array_keys(ModuleManager::listAllEnabledModules());

        // Phase 1: 执行 Seed 填充（先系统，再模块）
        $this->comment('[ Phase 1 ] Seed');
        $this->runPhase('seed', $enabledModules, 'Seed');

        // Phase 2: 执行 API 测试（先系统，再模块）
        $this->comment('[ Phase 2 ] API Tests');
        $this->runPhase('api', $enabledModules, 'Api');

        // Phase 3: 执行 Biz 测试（先系统，再模块）
        $this->comment('[ Phase 3 ] Biz Tests');
        $this->runPhase('biz', $enabledModules, 'Biz');

        // 输出汇总
        $this->info('');
        $this->info('=== 测试汇总 ===');
        $this->info('通过: ' . TestContext::getPassed());
        if (TestContext::hasFailure()) {
            $this->error('失败: ' . TestContext::getFailed());
            foreach (TestContext::getFailures() as $failure) {
                $this->error('  [FAIL] ' . $failure['name']);
                if ($failure['reason']) {
                    $this->error('         ' . $failure['reason']);
                }
                if ($failure['file']) {
                    $this->error('         in ' . $failure['file']);
                }
            }
            return 1;
        } else {
            $this->info('失败: 0');
            $this->info('');
            $this->info('所有测试通过！');
            return 0;
        }
    }

    /**
     * 运行一个阶段的所有脚本文件
     *
     * @param string $systemDir  /test/ 下的子目录名，如 seed / api / biz
     * @param array  $modules    已启用模块名列表
     * @param string $moduleDir  模块 Test/ 下的子目录名，如 Seed / Api / Biz
     */
    private function runPhase($systemDir, $modules, $moduleDir)
    {
        // 先运行系统测试目录
        $systemPath = base_path('test/' . $systemDir);
        $this->runFilesInDir($systemPath);

        // 再运行各模块测试目录
        foreach ($modules as $module) {
            $modulePath = ModuleManager::path($module, 'Test/' . $moduleDir);
            $this->runFilesInDir($modulePath);
        }
    }

    /**
     * 运行目录下的所有 .php 文件
     *
     * @param string $dir
     */
    private function runFilesInDir($dir)
    {
        if (!is_dir($dir)) {
            return;
        }
        $files = glob($dir . '/*.php');
        if (empty($files)) {
            return;
        }
        foreach ($files as $file) {
            $this->runFile($file);
        }
    }

    /**
     * 加载并执行单个测试文件，捕获异常记录为失败
     *
     * @param string $file
     */
    private function runFile($file)
    {
        $relativePath = str_replace(base_path('/'), '', $file);
        $this->line('  > ' . $relativePath);
        TestContext::setCurrentFile($relativePath);
        try {
            include $file;
        } catch (\Exception $e) {
            TestContext::fail($relativePath, $e->getMessage());
            $this->error('    [ERROR] ' . $e->getMessage());
        }
    }

    /**
     * 校验 git 修改过的迁移文件类名是否与文件名推导一致
     *
     * Laravel 的 Migrator::resolve() 根据迁移文件名推导类名：
     *   $file = implode('_', array_slice(explode('_', $file), 4));
     *   $class = Str::studly($file);
     * 若类名与推导结果不一致，migrate 时会报 "Class not found"。
     * 只校验未提交（git 修改/新增）的迁移文件，避免影响历史已发布文件。
     *
     * @return bool 校验通过返回 true
     */
    private function checkMigrationClassNames()
    {
        $files = $this->getChangedMigrationFiles();
        if (empty($files)) {
            $this->info('  没有修改过的迁移文件，或 git 不可用，跳过类名校验');
            return true;
        }
        $fail = false;
        foreach ($files as $file) {
            // 模拟 Laravel Migrator::resolve() 的类名推导
            $filename = basename($file, '.php');
            $parts = explode('_', $filename);
            $namePart = implode('_', array_slice($parts, 4));
            $expectedClass = \Illuminate\Support\Str::studly($namePart);

            $content = @file_get_contents($file);
            if ($content === false) {
                $this->error('  无法读取迁移文件: ' . $file);
                $fail = true;
                continue;
            }
            if (!preg_match('/class\s+(\w+)\s+extends\s+Migration/', $content, $m)) {
                $this->error('  ' . $filename . ' 未找到 "class Xxx extends Migration" 声明');
                $fail = true;
                continue;
            }
            $actualClass = $m[1];
            if ($actualClass !== $expectedClass) {
                $this->error('  ' . $filename . ' 类名为 ' . $actualClass . '，但 Laravel 按文件名推导应为 ' . $expectedClass . '（迁移类名必须与文件名保持一致）');
                $fail = true;
            }
        }
        if ($fail) {
            return false;
        }
        $this->info('  迁移文件类名校验通过（共 ' . count($files) . ' 个修改的迁移文件）');
        return true;
    }

    /**
     * 获取 git 修改/新增的迁移文件列表
     *
     * 覆盖系统迁移目录（database/migrations/）与所有模块迁移目录（module 下各模块的 Migrate/ 目录）
     *
     * @return array 迁移文件绝对路径列表；git 不可用或非 git 仓库时返回空数组（跳过校验）
     */
    private function getChangedMigrationFiles()
    {
        $gitRoot = base_path();
        $files = [];
        // 不带路径过滤获取全部变更，避免 git pathspec glob 不可靠的问题
        $cmd = 'git -C ' . escapeshellarg($gitRoot) . ' status --porcelain --untracked-files=all';
        $output = [];
        exec($cmd, $output, $exitCode);
        if ($exitCode !== 0) {
            // git 不可用或非 git 仓库时无法判断哪些文件是本次修改的，跳过校验
            return $files;
        }
        foreach ($output as $line) {
            $relPath = trim(substr($line, 3)); // porcelain 格式: "XY path"，去掉前3个字符(状态2位+空格)
            if ($relPath === '') {
                continue;
            }
            // 处理 rename 情况： "old -> new"
            if (strpos($relPath, ' -> ') !== false) {
                $relPath = trim(substr($relPath, strpos($relPath, ' -> ') + 4));
            }
            // 仅保留迁移目录下的 PHP 文件：系统迁移 或 模块迁移
            if (!preg_match('/(^|\/)database\/migrations\/.+\.php$/', $relPath)
                && !preg_match('/(^|\/)Migrate\/.+\.php$/', $relPath)) {
                continue;
            }
            $absPath = $gitRoot . '/' . $relPath;
            if (is_file($absPath)) {
                $files[] = $absPath;
            }
        }
        $files = array_unique($files);
        sort($files);
        return $files;
    }

}
