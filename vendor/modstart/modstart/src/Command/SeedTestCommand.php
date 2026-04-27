<?php

namespace ModStart\Command;

use Illuminate\Console\Command;
use ModStart\Module\ModuleManager;
use ModStart\Test\TestContext;

class SeedTestCommand extends Command
{
    protected $signature = 'modstart:seed-test';
    protected $description = '执行系统自动化测试（Seed 填充 + API 测试 + Biz 测试）';

    public function handle()
    {
        // 安全校验：仅允许在指定测试数据库配置下执行，防止误操作生产环境
        $requiredEnv = [
            'DB_HOST'     => 'docker-master',
            'DB_USERNAME' => 'root',
            'DB_PASSWORD' => '123456',
        ];
        foreach ($requiredEnv as $key => $expected) {
            $actual = env($key);
            if ($actual !== $expected) {
                $this->error('  安全校验失败：' . $key . ' 期望值为 "' . $expected . '"，实际值为 "' . $actual . '"');
                $this->error('  请确认当前环境为测试环境后再执行此命令。');
                return 1;
            }
        }

        TestContext::reset();

        $this->info('');
        $this->info('=== modstart:seed-test ===');
        $this->info('');

        // Step 1: 删除所有数据库表
        $this->comment('[ Step 1 ] 删除所有数据库表');
        try {
            \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0');
            $tables = \Illuminate\Support\Facades\DB::select('SHOW TABLES');
            foreach ($tables as $table) {
                $tableName = array_values((array)$table)[0];
                \Illuminate\Support\Facades\Schema::dropIfExists($tableName);
                $this->line('  > 删除表: ' . $tableName);
            }
            \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1');
            $this->info('  所有表已删除');
        } catch (\Exception $e) {
            $this->error('  删除表失败: ' . $e->getMessage());
            return 1;
        }

        // Step 2: 运行数据库迁移
        $this->comment('[ Step 2 ] 运行 migrate');
        if ($this->runArtisanProcess('migrate --force') !== 0) {
            $this->error('  migrate 失败，终止执行');
            return 1;
        }

        // Step 3: 安装所有模块（部分模块可能有非致命错误，不中断）
        $this->comment('[ Step 3 ] 运行 modstart:module-install-all');
        $exitCode = $this->runArtisanProcess('modstart:module-install-all');
        if ($exitCode !== 0) {
            $this->warn('  module-install-all 返回非零退出码（' . $exitCode . '），存在部分模块错误，继续执行');
        }

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
     * 在独立子进程中运行 artisan 命令，实时输出结果
     *
     * @param string $artisanArgs  artisan 命令及参数，如 "migrate --force"
     * @return int 退出码
     */
    private function runArtisanProcess($artisanArgs)
    {
        $php = PHP_BINARY;
        $artisan = base_path('artisan');
        $cmd = escapeshellarg($php) . ' ' . escapeshellarg($artisan) . ' ' . $artisanArgs . ' 2>&1';
        $handle = popen($cmd, 'r');
        if ($handle === false) {
            $this->error('  无法启动子进程');
            return 1;
        }
        while (!feof($handle)) {
            $line = fgets($handle);
            if ($line !== false && trim($line) !== '') {
                $this->line('  ' . rtrim($line));
            }
        }
        return pclose($handle);
    }
}
