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
     * 运行命令
     * @param $command string 命令
     * @param $log bool 是否记录日志
     * @return string
     */
    public static function run($command, $log = true)
    {
        if ($log) {
            Log::info("ShellUtil.run -> " . $command);
        }
        $ret = shell_exec($command);
        $ret = @trim($ret);
        if ($log) {
            Log::info("ShellUtil.run.result -> " . $ret);
        }
        return $ret;
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
