<?php


namespace Module\Vendor\Util;

use ModStart\Core\Util\PlatformUtil;
use ModStart\Core\Util\ReUtil;

include_once __DIR__ . '/../Shell/include.php';

class UniappUtil
{
    public static function runOrFail($command)
    {
        shell_echo_info("开始运行 $command");
        passthru($command, $resultCode);
        if ($resultCode !== 0) {
            shell_echo_error('运行命令 ' . $command . ' 失败');
            exit(1);
        }
    }

    public static function build($dir)
    {
        $module = ReUtil::group1('/module[\/\\\\](.*?)[\/\\\\]resources/', $dir);

        shell_echo_block("检查环境");
        shell_throws_if("请进入 " . $dir . "目录再运行该脚本", getcwd() !== $dir);
        shell_throws_if("系统只持支Linux和OSX", !PlatformUtil::isType([PlatformUtil::LINUX, PlatformUtil::OSX]));
        shell_throws_if("请安装 nodejs，安装后自检 npm --version 输出正确", !shell_command_check('npm --version'));
        shell_throws_if("解析模块名称失败", empty($module));
        shell_echo_success('环境正常');

        shell_echo_block("开始编译");
        self::runOrFail("npm install");
        self::runOrFail("npm run build:h5");

        shell_echo_block("处理HTML文件");
        $content = file_get_contents('dist/build/h5/index.html');
        preg_match('/<head>([\\s\\S]+)<\\/head>[\\s\\S]*<body>([\\s\\S]+)<\\/body>/', $content, $mat);
        $head = $mat[1];
        $body = $mat[2];
        $replaces = [
            '__cdn_url__/' => '{{\ModStart\Core\Assets\AssetsUtil::cdn()}}vendor/' . $module . '/',
        ];
        $headNew = str_replace(array_keys($replaces), array_values($replaces), $head);
        $bodyNew = str_replace(array_keys($replaces), array_values($replaces), $body);
        shell_echo_info("处理Head : " . strlen($head) . ' -> ' . strlen($headNew));
        shell_echo_info("处理Body : " . strlen($body) . ' -> ' . strlen($bodyNew));
        $headPath = shell_module_path($module, 'View/m/' . lcfirst($module) . '/head.blade.php');
        $bodyPath = shell_module_path($module, 'View/m/' . lcfirst($module) . '/body.blade.php');
        shell_file_write($headPath, $headNew);
        shell_file_write($bodyPath, $bodyNew);

        shell_echo_block("处理静态文件");
        $replaces = [
            '"__cdn_url__/"' => 'window.__msCDN+"vendor/' . $module . '/"',
            // '(/static/' => '(/vendor/' . $module . '/static/',
            '"/static/' => 'window.__msCDN+"vendor/' . $module . '/static/',
        ];
        $files = glob('dist/build/h5/static/js/*.js');
        foreach ($files as $file) {
            shell_echo_info($file);
            $content = file_get_contents($file);
            $content = str_replace(array_keys($replaces), array_values($replaces), $content);
            file_put_contents($file, $content);
        }

        shell_echo_block("更新模块静态资源");
        shell_ensure_dir(shell_module_path($module, 'Asset'));
        shell_echo_info('清空目录');
        passthru("rm -rfv " . shell_module_path($module, 'Asset/static/'));
        shell_echo_info('更新文件');
        passthru("cp -av dist/build/h5/static " . shell_module_path($module, 'Asset/static/'));
        shell_echo_success('打包完成');

        shell_echo_block("温馨提示");
        shell_echo_info('如果您的正在运行的网站还没有更新静态资源，您需要运行如下命令更新模块静态资源');
        shell_echo_info("php artisan modstart:module-install $module --force");
    }
}
