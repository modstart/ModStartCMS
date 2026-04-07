<?php

/**
 * 检测当前项目是否为 Laravel 9
 */
function artisan_check_is_laravel9()
{
    $composerFile = __DIR__ . '/../../../composer.json';
    if (file_exists($composerFile)) {
        $content = file_get_contents($composerFile);
        return strpos($content, 'modstart/modstart-laravel9') !== false;
    }
    return false;
}

/**
 * 获取 PHP 版本要求说明
 */
function artisan_check_php_version_requires()
{
    if (artisan_check_is_laravel9()) {
        return '8.1.x';
    }
    return '5.6.x 或 7.0.x';
}

/**
 * 检测当前 PHP 版本是否满足要求
 */
function artisan_check_php_version_ok()
{
    if (artisan_check_is_laravel9()) {
        if (version_compare(PHP_VERSION, '8.1.0', '<')) {
            return false;
        }
        return true;
    }
    if (version_compare(PHP_VERSION, '5.5.9', '<')) {
        return false;
    }
    if (version_compare(PHP_VERSION, '7.1.0', '>=')) {
        return false;
    }
    return true;
}

/**
 * 检测是否为 Linux 系统
 */
function artisan_check_is_linux()
{
    return strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN' && PHP_OS !== 'Darwin';
}

/**
 * 检测是否为宝塔环境（路径在 /www/wwwroot/ 下且存在 bt 命令）
 */
function artisan_check_is_bt()
{
    $cwd = __DIR__;
    if (strpos($cwd, '/www/wwwroot/') === false) {
        return false;
    }
    exec('which bt 2>/dev/null', $output, $ret);
    return $ret === 0;
}

/**
 * 获取宝塔环境建议使用的 PHP 可执行文件路径
 */
function artisan_check_bt_suggest_php()
{
    if (artisan_check_is_laravel9()) {
        return '/www/server/php/81/bin/php';
    }
    $candidates = [
        '/www/server/php/56/bin/php',
        '/www/server/php/70/bin/php',
    ];
    foreach ($candidates as $php) {
        if (file_exists($php)) {
            return $php;
        }
    }
    return $candidates[0];
}

// === 检测 root 用户 ===
if (artisan_check_is_linux()) {
    if (function_exists('posix_getuid')) {
        $uid = posix_getuid();
        if ($uid == 0) {
            echo "You can't run this command as root ( uid = ${uid} ).\n";
            if (artisan_check_is_bt()) {
                $suggestPhp = artisan_check_bt_suggest_php();
                echo "宝塔环境建议使用以下命令替代 php artisan：\n";
                echo "  sudo -u www {$suggestPhp} artisan\n";
            } else {
                echo "建议切换到非 root 用户后执行：\n";
                echo "  sudo -u www php artisan\n";
            }
            exit(-1);
        }
    }
}

// === 检测 PHP 版本 ===
if (!artisan_check_php_version_ok()) {
    echo "PHP 版本不满足要求，当前版本：" . PHP_VERSION . "，要求版本：" . artisan_check_php_version_requires() . "\n";
    if (artisan_check_is_bt()) {
        $suggestPhp = artisan_check_bt_suggest_php();
        echo "宝塔环境建议使用以下命令替代 php artisan：\n";
        echo "  {$suggestPhp} artisan\n";
    }
    exit(-1);
}
