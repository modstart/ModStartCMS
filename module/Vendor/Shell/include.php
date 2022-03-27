<?php

include __DIR__ . '/../../../vendor/modstart/modstart/src/Core/Util/PlatformUtil.php';
include __DIR__ . '/../../../vendor/modstart/modstart/src/Core/Util/ReUtil.php';

function shell_module_base()
{
    return realpath(__DIR__ . '/../..');
}

function shell_module_path($module, $path)
{
    return join('/', [
        rtrim(shell_module_base(), '/'),
        $module,
        $path
    ]);
}

function shell_ensure_dir($dir)
{
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
    }
}

function shell_echo_block($msg)
{
    echo "\n\e[33m";
    echo ' ' . str_repeat('-', 80) . "\n";
    echo sprintf('| %-79s|', $msg) . "\n";
    echo ' ' . str_repeat('-', 80) . "\n";
    echo "\e[0m";
}

function shell_echo_error($msg)
{
    echo "\033[31m>>>>> ERROR : $msg \033[0m\n";
}

function shell_echo_success($msg)
{
    echo "\033[32m>>>>> INFO  : $msg \033[0m\n";
}

function shell_echo_info($msg)
{
    echo "\033[36m>>>>> INFO  : $msg \033[0m\n";
}

function shell_throws_if($msg, $boolean)
{
    if ($boolean) {
        shell_echo_error($msg);
        exit(-1);
    }
}

function shell_command_check($command)
{
    @exec($command, $output, $ret);
    return $ret === 0;
}

function shell_file_write($filepath, $content)
{
    $dir = dirname($filepath);
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
    }
    file_put_contents($filepath, $content);
}
