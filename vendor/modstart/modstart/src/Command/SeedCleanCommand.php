<?php

namespace ModStart\Command;

use Illuminate\Console\Command;

class SeedCleanCommand extends Command
{
    use SeedTestTrait;

    protected $signature = 'modstart:seed-clean';
    protected $description = '清理并重置测试数据库（删除表 + 迁移 + 模块安装 + 初始化管理员）';

    public function handle()
    {
        if (!$this->checkTestEnvironment()) {
            return 1;
        }

        $this->info('');
        $this->info('=== modstart:seed-clean ===');
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

        $this->info('');
        $this->info('=== 测试数据库已重置完成 ===');
        $this->info('');

        return 0;
    }
}
