<?php

namespace ModStart\Command;

trait SeedTestTrait
{
    /**
     * 测试运行锁文件句柄
     *
     * @var resource|null
     */
    private $runLockHandle = null;

    /**
     * 获取测试运行锁，确保同一时间只有一个 seed-test 在运行
     *
     * 锁文件位于项目 _temp 目录（base_path('_temp/seed-test.lock')），内容记录持有进程
     * 的 PID 与启动时间，便于并发冲突时判断持有进程是否存活。
     *
     * 使用 flock 排他锁：进程正常结束或异常退出（崩溃、SIGINT、kill -9）时，
     * 锁由操作系统自动释放，因此不会出现锁文件残留、需要人工清理的问题。
     * 锁文件本身保留在磁盘上不删除，避免删除后重建 inode 引发锁失效。
     *
     * 唯一例外：UI 阶段由 proc_open 启动的常驻子进程（php artisan serve）
     * 会继承锁的文件描述符；主进程被 kill -9 时该子进程不会收到信号，
     * 会继续持有锁。此时记录的主进程 PID 已不存在，可通过 PID 存活判断
     * 识别为残留锁并自动重建锁文件（见下方 acquireRunLock 实现）。
     *
     * @return bool 获取成功返回 true；已有实例在运行时提示并返回 false
     */
    private function acquireRunLock()
    {
        $lockDir = base_path('_temp');
        if (!is_dir($lockDir) && !@mkdir($lockDir, 0755, true) && !is_dir($lockDir)) {
            $this->error('  无法创建测试锁目录: ' . $lockDir);
            return false;
        }
        $lockFile = $lockDir . '/seed-test.lock';
        // guard 锁：串行化「残留锁检测与重建」临界区，避免多个进程同时抢锁。
        // 临界区极短且用完立即关闭，不会被后续 proc_open 子进程继承。
        $guardFile = $lockFile . '.guard';
        $guard = @fopen($guardFile, 'c');
        if ($guard === false) {
            $this->error('  无法创建测试锁文件: ' . $guardFile);
            return false;
        }
        flock($guard, LOCK_EX);

        $handle = @fopen($lockFile, 'c');
        if ($handle === false) {
            flock($guard, LOCK_UN);
            fclose($guard);
            $this->error('  无法创建测试锁文件: ' . $lockFile);
            return false;
        }

        $locked = flock($handle, LOCK_EX | LOCK_NB);
        $holderPid = 0;
        if (!$locked) {
            $content = @file_get_contents($lockFile);
            $holderPid = $this->parseRunLockPid($content);
            // 锁被占用：若记录的主进程已不存在，说明是异常退出后的残留锁
            // （主进程被 kill -9 时，继承锁 fd 的 php artisan serve 子进程仍在运行）。
            // 删除并重建锁文件，得到新 inode，与残留子进程持有的旧锁隔离。
            if ($holderPid > 0 && !$this->isProcessAlive($holderPid)) {
                $this->warn('  检测到残留测试锁（PID: ' . $holderPid . ' 已退出），已自动忽略该锁。');
                fclose($handle);
                @unlink($lockFile);
                $handle = @fopen($lockFile, 'c');
                $locked = ($handle !== false) && flock($handle, LOCK_EX | LOCK_NB);
            }
        }

        if (!$locked) {
            if ($handle) {
                fclose($handle);
            }
            flock($guard, LOCK_UN);
            fclose($guard);
            // 打印持有锁的 PID，便于人工判断该进程是否已挂（挂了可手动清理）
            $startedAt = isset($content) ? $this->parseRunLockStarted($content) : '';
            $holderInfo = $holderPid > 0
                ? 'PID: ' . $holderPid . ($startedAt !== '' ? '，开始时间: ' . $startedAt : '')
                : '无法获取持有进程 PID';
            $this->error('');
            $this->error('  已有 modstart:seed-test 正在运行（' . $holderInfo . '），请等待其结束后再重试。');
            $this->error('');
            return false;
        }

        // 写入持有者信息（PID + 启动时间），便于并发时排查
        ftruncate($handle, 0);
        rewind($handle);
        fwrite($handle, 'pid=' . getmypid() . ' started=' . date('Y-m-d H:i:s'));
        fflush($handle);
        $this->runLockHandle = $handle;

        flock($guard, LOCK_UN);
        fclose($guard);
        return true;
    }

    /**
     * 从锁文件内容解析持有者 PID
     *
     * @param string|false $content
     * @return int 解析失败返回 0
     */
    private function parseRunLockPid($content)
    {
        if (!is_string($content) || $content === '') {
            return 0;
        }
        if (preg_match('/pid=(\d+)/', $content, $m)) {
            return (int)$m[1];
        }
        return 0;
    }

    /**
     * 从锁文件内容解析持有者启动时间
     *
     * @param string|false $content
     * @return string 解析失败返回空字符串
     */
    private function parseRunLockStarted($content)
    {
        if (!is_string($content) || $content === '') {
            return '';
        }
        if (preg_match('/started=([0-9\-: ]+)/', $content, $m)) {
            return trim($m[1]);
        }
        return '';
    }

    /**
     * 判断指定 PID 的进程是否存活
     *
     * @param int $pid
     * @return bool
     */
    private function isProcessAlive($pid)
    {
        if (!function_exists('posix_kill')) {
            // 无 posix 扩展时保守认为进程存活，避免误删正在使用的锁
            return true;
        }
        $alive = @posix_kill((int)$pid, 0);
        if (!$alive && function_exists('posix_get_last_error') && posix_get_last_error() === 1) {
            // errno 1 = EPERM，进程存在但无权限发送信号
            $alive = true;
        }
        return $alive;
    }

    /**
     * 释放测试运行锁
     */
    private function releaseRunLock()
    {
        if ($this->runLockHandle) {
            flock($this->runLockHandle, LOCK_UN);
            fclose($this->runLockHandle);
            $this->runLockHandle = null;
        }
    }

    /**
     * 安全校验：仅允许在指定测试数据库配置下执行，防止误操作生产环境
     *
     * @return bool 校验通过返回 true，失败返回 false
     */
    private function checkTestEnvironment()
    {
        // DB_HOST 允许 docker-master（Docker 测试环境）或 127.0.0.1（本地环境）
        $allowedDbHosts = ['docker-master', '127.0.0.1'];
        $dbHost = env('DB_HOST');
        if (!in_array($dbHost, $allowedDbHosts)) {
            $this->error('  安全校验失败：DB_HOST 期望值为 "' . implode('" 或 "', $allowedDbHosts) . '"，实际值为 "' . $dbHost . '"');
            $this->error('  请确认当前环境为测试环境后再执行此命令。');
            return false;
        }
        $requiredEnv = [
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
