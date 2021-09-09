<?php


namespace ModStart\Core\Util;


use Symfony\Component\Process\Process;

class ShellUtil
{
    public static function runCommandInPath($command, $path)
    {
        $process = new Process($command, $path);
        $process->setTimeout(180);
        $process->enableOutput();
        $process->run();
        $process->getStopSignal();
        return $process->getOutput();
    }
}