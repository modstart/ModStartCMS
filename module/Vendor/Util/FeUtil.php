<?php


namespace Module\Vendor\Util;

use ModStart\Core\Type\TypeUtil;
use ModStart\Core\Util\ConstantUtil;
use ModStart\Core\Util\FileUtil;
use ModStart\Core\Util\SerializeUtil;
use Module\Vendor\Support\ResponseCodes;

include_once __DIR__ . '/../Shell/include.php';

class FeUtil
{
    public static function tools($dir)
    {
        $argc = $GLOBALS['argc'];
        $argv = $GLOBALS['argv'];
        $basePath = rtrim($dir, '/') . '/';
        $config = [];
        switch ($argv[1]) {
            case 'DumpConstant':
                $jsonFilePath = $basePath . 'tools.config.json';
                shell_throws_if('文件不存在 ' . $jsonFilePath, !file_exists($jsonFilePath));
                $config = json_decode(file_get_contents($jsonFilePath), true);
                break;
        }
        $config = array_merge([

            'module' => null,
            'feModule' => null,

            'constants' => [],
            'constantsScript' => null,
            'constantsAppend' => null,

        ], $config);
        if (empty($config['module'])) {
            if (!preg_match('/module[\/\\\\](.*?)[\/\\\\]resources\\/(.*?)\\/?$/', $dir, $mat)) {
                shell_echo_error('目录不正确');
                exit(1);
            }
            $config['module'] = $mat[1];
            $config['feModule'] = $mat[2];
        }
        if (empty($config['constantsScript'])) {
            $configDir = ($config['feModule'] === 'mobile' ? 'config' : 'lib');
            $config['constantsScript'] = "{$basePath}src/{$configDir}/constant.js";
        }

        shell_echo_block("Uniapp Tools");
        if ($argc < 2) {
            shell_echo_error('参数错误');
            shell_echo('php tools.php DumpConstant → 构建 constant.js');
            shell_echo('php tools.php SyncAsset → 同步公共静态资源');
            exit(1);
        }

        switch ($argv[1]) {
            case 'SyncAsset':
                shell_echo_info("同步开始");
                $toBase = $basePath . 'src/static/image/';
                $files = FileUtil::listAllFiles($basePath . 'src/brick/image');
                foreach ($files as $file) {
                    if (!$file['isFile']) {
                        continue;
                    }
                    $to = $toBase . $file['filename'];
                    FileUtil::copy($file['pathname'], $to);
                    shell_echo_info("同步 {$file['pathname']}");
                }
                shell_echo_info("同步结束");
                break;
            case 'DumpConstant':
                shell_throws_if('请配置 constants', empty($config['constants']));
                $constants = [];
                foreach ($config['constants'] as $name) {
                    switch ($name) {
                        case 'ResponseCodes':
                            $constants['ResponseCodes'] = ConstantUtil::dump(ResponseCodes::class);
                            shell_echo_info("导出 {$name}");
                            break;
                        default:
                            shell_throws_if('类型不存在 ' . $name, !class_exists($name));
                            $constants[class_basename($name)] = TypeUtil::dump($name);
                            shell_echo_info("导出 {$name}");
                            break;
                    }
                }
                $content = [];
                $content [] = "// This file is created by -> php tools.php DumpConstant";
                foreach ($constants as $name => $json) {
                    $content[] = "export const $name = " . SerializeUtil::jsonEncodePretty($json) . ";";
                }
                if (!empty($config['constantsAppend'])) {
                    $content[] = $config['constantsAppend'];
                }
                $content = implode("\n\n", $content);
                FileUtil::write($config['constantsScript'], $content);
                shell_echo_info("导出完成 {$config['constantsScript']}");
                break;
            default:
                shell_echo_error('不支持的命令 ' . $argv[1]);
                exit(1);
        }
    }
}
