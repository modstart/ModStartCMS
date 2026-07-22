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

}
