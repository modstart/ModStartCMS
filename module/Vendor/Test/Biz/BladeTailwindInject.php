<?php
use ModStart\Test\TestCase;

TestCase::describe('Vendor 命令 Vendor:BladeTailwindInject', function () {

    TestCase::test('命令已注册', function () {
        $commands = array_keys(\Illuminate\Support\Facades\Artisan::all());
        TestCase::assertTrue(
            in_array('Vendor:BladeTailwindInject', $commands),
            'Vendor:BladeTailwindInject 命令已注册'
        );
    });

    TestCase::test('编译 Blade 中使用的 TailwindCSS 并写入独立 CSS 文件', function () {
        // 编译依赖 Node.js，未安装时跳过
        $node = trim((string)@shell_exec('command -v node 2>/dev/null'));
        if ($node === '') {
            TestCase::assertTrue(true, '跳过：未检测到 Node.js');
            return;
        }

        $bladeRel = 'module/Vendor/View/pc/_tw_inject_test.blade.php';
        $blade = base_path($bladeRel);
        $css = base_path('module/Vendor/Asset/css/page/_tw_inject_test.css');
        $source = "@extends(\$_viewFrame)\n\n@section('bodyContent')\n"
            . "    <div id=\"pageContainer\">\n"
            . "        <h1 class=\"text-2xl md:text-3xl font-bold text-blue-600\">测试</h1>\n"
            . "        <div class=\"grid grid-cols-1 md:grid-cols-2 gap-4 p-4\">\n"
            . "            <div class=\"bg-white rounded\">卡片</div>\n"
            . "        </div>\n"
            . "    </div>\n@endsection\n";

        try {
            if (!is_dir(dirname($blade))) {
                @mkdir(dirname($blade), 0755, true);
            }
            file_put_contents($blade, $source);

            $exitCode = \Illuminate\Support\Facades\Artisan::call('Vendor:BladeTailwindInject', [
                '--html' => [$bladeRel],
            ]);

            TestCase::assertEquals(0, $exitCode, '命令退出码为 0');
            TestCase::assertTrue(file_exists($css), '已生成独立 CSS 文件');
            if (file_exists($css)) {
                $cssContent = file_get_contents($css);
                TestCase::assertContains('#pageContainer .grid', $cssContent, 'CSS 已加 #pageContainer 作用域前缀');
            }
            $bladeContent = file_get_contents($blade);
            TestCase::assertContains('id="pageStyle"', $bladeContent, 'Blade 已注入 <link id="pageStyle">');
        } finally {
            @unlink($blade);
            @unlink($css);
            @rmdir(dirname($css));
            @rmdir(dirname(dirname($css)));
        }
    });
});
