<?php
use ModStart\Data\DataManager;
use ModStart\Test\TestCase;
use ModStart\Test\TestHttp;

/**
 * Member UEditor 访客接口安全测试
 * 覆盖：未登录 SSRF 抓取内网地址被拒绝、未登录 SVG 上传内容被清洗（存储型 XSS）
 */
TestCase::describe('Member UEditor 访客接口安全', function () {

    TestCase::test('catch 抓取内网/保留地址被拒绝', function () {
        $urls = [
            'http://127.0.0.1/x.jpg',
            'http://169.254.169.254/latest/meta-data/',
            'http://10.0.0.1/x.jpg',
            'http://192.168.1.1/x.jpg',
            'http://localhost/x.jpg',
        ];
        foreach ($urls as $url) {
            $ret = TestHttp::post('/member_data/ueditor_guest', [
                'action' => 'catch',
                'source' => [$url],
            ]);
            TestCase::assertTrue(isset($ret['state']) && 'SUCCESS' === $ret['state'], 'catch 接口返回合法响应: ' . $url);
            $item = isset($ret['list'][0]) ? $ret['list'][0] : null;
            TestCase::assertTrue($item !== null, 'catch 返回 list 项: ' . $url);
            TestCase::assertTrue('SUCCESS' !== $item['state'], '内网地址未被抓取: ' . $url);
            TestCase::assertTrue(empty($item['url']), '内网地址未生成文件地址: ' . $url);
        }
    });

    TestCase::test('上传 SVG 中的脚本被清洗', function () {
        $tmp = sys_get_temp_dir() . '/xss_' . mt_rand(1000, 9999) . '.svg';
        file_put_contents($tmp, '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(document.domain)</script><rect width="10" height="10"/></svg>');

        $ret = TestHttp::postFile('/member_data/ueditor_guest', ['action' => 'image'], 'file', $tmp);
        @unlink($tmp);

        TestCase::assertTrue(isset($ret['state']) && 'SUCCESS' === $ret['state'], 'SVG 上传返回成功');
        TestCase::assertTrue(!empty($ret['url']), 'SVG 上传返回文件地址');

        // 读取实际存储内容，确认脚本已被清洗
        $path = parse_url($ret['url'], PHP_URL_PATH);
        $local = public_path(ltrim($path, '/'));
        TestCase::assertTrue(file_exists($local), '上传文件已落盘: ' . $local);
        $content = file_get_contents($local);
        TestCase::assertTrue(stripos($content, '<script') === false, '存储内容不应包含 script 标签');
        TestCase::assertTrue(stripos($content, 'alert') === false, '存储内容不应包含脚本代码');

        // 清理上传文件
        DataManager::deleteByPath($path);
    });
});
