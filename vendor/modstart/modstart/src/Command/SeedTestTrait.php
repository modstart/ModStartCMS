<?php

namespace ModStart\Command;

trait SeedTestTrait
{
    /**
     * 安全校验：仅允许在指定测试数据库配置下执行，防止误操作生产环境
     *
     * @return bool 校验通过返回 true，失败返回 false
     */
    private function checkTestEnvironment()
    {
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
                return false;
            }
        }
        return true;
    }

    /**
     * 删除所有数据库表
     *
     * @return bool 成功返回 true，失败返回 false
     */
    private function dropAllTables()
    {
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
            return true;
        } catch (\Exception $e) {
            $this->error('  删除表失败: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 运行数据库迁移
     *
     * @return bool 成功返回 true，失败返回 false
     */
    private function runMigrate()
    {
        $this->comment('  运行 migrate');
        if ($this->runArtisanProcess('migrate --force') !== 0) {
            $this->error('  migrate 失败，终止执行');
            return false;
        }
        return true;
    }

    /**
     * 安装所有模块
     */
    private function installAllModules()
    {
        $this->comment('  运行 modstart:module-install-all');
        $exitCode = $this->runArtisanProcess('modstart:module-install-all');
        if ($exitCode !== 0) {
            $this->warn('  module-install-all 返回非零退出码（' . $exitCode . '），存在部分模块错误，继续执行');
        }
    }

    /**
     * 初始化默认超级管理员（admin / 123456）
     */
    private function initDefaultAdmin()
    {
        try {
            $adminUserClass = \ModStart\Admin\Model\AdminUser::class;
            $count = \ModStart\Core\Dao\ModelUtil::count($adminUserClass);
            if ($count == 0) {
                \ModStart\Admin\Auth\Admin::add('admin', '123456');
                $this->info('  默认超级管理员已创建：admin / 123456');
            } else {
                $this->info('  管理员用户已存在，跳过创建（共 ' . $count . ' 个）');
            }
        } catch (\Exception $e) {
            $this->warn('  创建默认超级管理员失败: ' . $e->getMessage() . '，继续执行');
        }
    }

    /**
     * 在独立子进程中运行 artisan 命令，实时输出结果
     *
     * @param string $artisanArgs artisan 命令及参数，如 "migrate --force"
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
