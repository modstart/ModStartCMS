<?php


namespace ModStart\Core\Util;


use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class ShellUtil
{
    /**
     * 在指定路径下执行命令
     * @param $command string 命令
     * @param $path string 路径
     * @return string
     */
    public static function runCommandInPath($command, $path)
    {
        $process = new Process($command, $path);
        $process->setTimeout(180);
        $process->enableOutput();
        $process->run();
        $process->getStopSignal();
        return $process->getOutput();
    }

    /**
     * 运行命令（该方法会共享当前进程的环境变量）
     * @param $command string 命令
     * @param $log bool 是否记录日志
     * @return string
     */
    public static function run($command, $log = true)
    {
        if ($log) {
            Log::info("ShellUtil.Run -> " . $command);
        }
        $ret = shell_exec($command);
        $ret = @trim($ret);
        if ($log) {
            Log::info("ShellUtil.Result -> " . $ret);
        }
        return $ret;
    }

    public static function runExec($command, &$exitCode, $log = true)
    {
        if ($log) {
            Log::info("ShellUtil.Run -> " . $command);
        }
        exec($command, $output, $exitCode);
        $output = @trim(join("\n", $output));
        if ($log) {
            Log::info("ShellUtil.Result -> " . $output);
        }
        return $output;
    }

    /**
     * 在新进程中运行命令
     * @param $command string 命令
     * @param $log bool 是否记录日志
     * @return string
     */
    public static function runInNewProcess($command, $log = true)
    {
        if ($log) {
            Log::info("ShellUtil.RunInNewProcess -> " . $command);
        }
        // 隔离父进程环境变量，避免污染子进程：
        // ScheduleRunAllCommand 会批量运行其他站点的 schedule:run，
        // 若子进程继承父进程（当前站点）的 .env 环境变量（SESSION_DRIVER、APP_NAME、DB_* 等），
        // 会导致子进程配置错乱（如 "Driver [smart_redis] not supported"）。
        // 这里通过 Process 的 env 参数完全替换子进程环境，仅保留必要变量（PATH / HOME），
        // 兼容 Windows（Path / USERPROFILE）、Linux、macOS。
        $env = array();
        $path = getenv('PATH');
        if ($path === false) {
            $path = getenv('Path');
        }
        if ($path === false) {
            $path = '/usr/local/bin:/usr/bin:/bin';
        }
        $env['PATH'] = (string)$path;
        $home = getenv('HOME');
        if ($home === false) {
            $home = getenv('USERPROFILE');
        }
        if ($home !== false && $home !== '') {
            $env['HOME'] = (string)$home;
        }
        $process = new Process($command, null, $env, null, null, array());
        $process->start();
        $process->wait();
        $ret = $process->getOutput();
        $ret = @trim($ret);
        if ($log) {
            Log::info("ShellUtil.Result -> " . $ret);
        }
        return $ret;
    }

    public static function runWithTimeout($command, $timeout = 60, $log = true)
    {
        if ($log) {
            Log::info("ShellUtil.Run -> " . $command);
        }
        $process = new Process($command);
        $process->setTimeout($timeout);
        try {
            $process->start();
            $process->wait();
            $ret = $process->getOutput();
            $ret = @trim($ret);
            if ($log) {
                Log::info("ShellUtil.Result -> " . $ret);
            }
            return $ret;
        } catch (\Exception $e) {
            if ($log) {
                Log::error("ShellUtil.Error -> " . $e->getMessage());
            }
            return "ERROR:" . $e->getMessage();
        }
    }

    /**
     * @param $commandFilter
     * @return array|string|null
     * @since 1.7.0
     */
    public static function commandStatus($commandFilter)
    {
        $cmd = "ps -eo pid,etimes,cmd";
        if (is_array($commandFilter)) {
            foreach ($commandFilter as $item) {
                $cmd .= " | grep '$item'";
            }
        } else {
            $cmd .= " | grep '$commandFilter'";
        }
        $cmd .= " | grep -v grep";
        $ret = trim(shell_exec($cmd));
        if (empty($ret)) {
            return null;
        }
        $pcs = preg_split('/\\s+/', $ret);
        if (count($pcs) >= 3) {
            $ret = [
                'pid' => intval($pcs[0]),
                'uptime' => intval($pcs[1]),
                'cmd' => '',
            ];
            array_shift($pcs);
            array_shift($pcs);
            $ret['cmd'] = join(' ', $pcs);
            return $ret;
        }
        return null;
    }

    /**
     * @param $command
     * @param null $outputFile
     * @since 1.7.0
     */
    public static function commandRunBackground($command, $outputFile = null)
    {
        if (null === $outputFile) {
            $outputFile = '/dev/null';
        }
        $cmd = "nohup $command >$outputFile 2>&1 &";
        shell_exec($cmd);
    }

    public static function cleanDir($dir, $keepMinute, $ext)
    {
        shell_exec("/usr/bin/find $dir -mmin +$keepMinute -name \"*.$ext\" -exec rm -rfv {} \;");
    }

    public static function cleanDirWithPattern($dir, $keepMinute, $pattern)
    {
        // /usr/bin/find / -maxdepth 1 -mmin +1 -name "core.*" -exec rm -rfv {} \;
        shell_exec("/usr/bin/find $dir -maxdepth 1 -mmin +$keepMinute -name \"$pattern\" -exec rm -rfv {} \;");
    }

    public static function isCli()
    {
        return php_sapi_name() == "cli";
    }

    public static function pathQuote($path)
    {
        return self::escapeArgument($path);
    }

    public static function escapeArgument($argument)
    {
        if ('' === $argument || null === $argument) {
            return '""';
        }
        if ('\\' !== \DIRECTORY_SEPARATOR) {
            return "'" . str_replace("'", "'\\''", $argument) . "'";
        }
        if (str_contains($argument, "\0")) {
            $argument = str_replace("\0", '?', $argument);
        }
        if (!preg_match('/[\/()%!^"<>&|\s]/', $argument)) {
            return $argument;
        }
        $argument = preg_replace('/(\\\\+)$/', '$1$1', $argument);

        return '"' . str_replace(['"', '^', '%', '!', "\n"], ['""', '"^^"', '"^%"', '"^!"', '!LF!'], $argument) . '"';
    }

    public static function runRealtime($cmd)
    {
        echo "\n[ShellRun] $cmd\n";
        if (($fp = popen($cmd, "r"))) {
            while (!feof($fp)) {
                echo fread($fp, 1024);
                flush();
            }
            fclose($fp);
        }
    }
}
