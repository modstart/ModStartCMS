<?php
use ModStart\Core\Exception\BizException;
use ModStart\Core\Util\FileUtil;
use ModStart\Test\TestCase;

/**
 * FileUtil SSRF 防护（savePathToLocalTemp ssrfConfig 选项）单元测试
 */

TestCase::assertTrue(class_exists('ModStart\Core\Util\FileUtil'), 'SSRF Biz: FileUtil 类可加载');

// 工具函数：断言校验抛出 BizException
function ssrfBizAssertRejected($url, $ssrfConfig, $name)
{
    $rejected = false;
    try {
        FileUtil::assertSsrfSafe($url, $ssrfConfig);
    } catch (BizException $e) {
        $rejected = true;
    }
    TestCase::assertTrue($rejected, $name);
}

// 工具函数：断言校验放行（不抛异常）
function ssrfBizAssertPassed($url, $ssrfConfig, $name)
{
    $passed = true;
    try {
        FileUtil::assertSsrfSafe($url, $ssrfConfig);
    } catch (BizException $e) {
        $passed = false;
    }
    TestCase::assertTrue($passed, $name);
}

// 未启用时不校验（默认 enable=false）
ssrfBizAssertPassed('http://127.0.0.1/x.jpg', ['enable' => false], 'SSRF Biz: enable=false 时内网地址放行（不校验）');

// 启用后拒绝内网/保留 IPv4
ssrfBizAssertRejected('http://127.0.0.1/x.jpg', ['enable' => true], 'SSRF Biz: 拒绝 127.0.0.1 回环');
ssrfBizAssertRejected('http://10.0.0.1/x.jpg', ['enable' => true], 'SSRF Biz: 拒绝 10.0.0.0/8 私有');
ssrfBizAssertRejected('http://172.16.0.1/x.jpg', ['enable' => true], 'SSRF Biz: 拒绝 172.16.0.0/12 私有');
ssrfBizAssertRejected('http://172.31.255.254/x.jpg', ['enable' => true], 'SSRF Biz: 拒绝 172.31.x.x 私有');
ssrfBizAssertRejected('http://192.168.1.1/x.jpg', ['enable' => true], 'SSRF Biz: 拒绝 192.168.0.0/16 私有');
ssrfBizAssertRejected('http://169.254.169.254/latest/meta-data/', ['enable' => true], 'SSRF Biz: 拒绝 169.254.169.254 云元数据');
ssrfBizAssertRejected('http://0.0.0.0/x.jpg', ['enable' => true], 'SSRF Biz: 拒绝 0.0.0.0 本机');
ssrfBizAssertRejected('http://100.64.0.1/x.jpg', ['enable' => true], 'SSRF Biz: 拒绝 100.64.0.0/10 运营商 NAT');
ssrfBizAssertRejected('http://224.0.0.1/x.jpg', ['enable' => true], 'SSRF Biz: 拒绝组播地址');
ssrfBizAssertRejected('http://240.0.0.1/x.jpg', ['enable' => true], 'SSRF Biz: 拒绝保留地址');

// 拒绝特殊主机名
ssrfBizAssertRejected('http://localhost/x.jpg', ['enable' => true], 'SSRF Biz: 拒绝 localhost');
ssrfBizAssertRejected('http://foo.localhost/x.jpg', ['enable' => true], 'SSRF Biz: 拒绝 *.localhost');

// IPv6 内网地址
ssrfBizAssertRejected('http://[::1]/x.jpg', ['enable' => true], 'SSRF Biz: 拒绝 IPv6 ::1 回环');
ssrfBizAssertRejected('http://[fc00::1]/x.jpg', ['enable' => true], 'SSRF Biz: 拒绝 IPv6 fc00::/7 ULA');
ssrfBizAssertRejected('http://[fe80::1]/x.jpg', ['enable' => true], 'SSRF Biz: 拒绝 IPv6 fe80::/10 链路本地');
ssrfBizAssertRejected('http://[::ffff:127.0.0.1]/x.jpg', ['enable' => true], 'SSRF Biz: 拒绝 IPv4 映射回环地址');

// 公网地址放行（纯校验不发请求）
ssrfBizAssertPassed('http://8.8.8.8/x.jpg', ['enable' => true], 'SSRF Biz: 放行公网 IP 8.8.8.8');
ssrfBizAssertPassed('http://1.1.1.1/x.jpg', ['enable' => true], 'SSRF Biz: 放行公网 IP 1.1.1.1');

// 自定义黑名单正则（匹配 host）
ssrfBizAssertRejected('http://evil.example.com/x.jpg', [
    'enable' => true,
    'blackRules' => ['<local>', '(^|\.)evil\.example\.com$'],
], 'SSRF Biz: 自定义黑名单正则拒绝 evil.example.com');

// 白名单模式：whiteRules 非空时仅允许命中白名单的 host
ssrfBizAssertPassed('http://example.com/x.jpg', [
    'enable' => true,
    'whiteRules' => ['(^|\.)example\.com$'],
], 'SSRF Biz: 白名单命中放行 example.com');
ssrfBizAssertRejected('http://other.com/x.jpg', [
    'enable' => true,
    'whiteRules' => ['(^|\.)example\.com$'],
], 'SSRF Biz: 白名单未命中拒绝 other.com');

// savePathToLocalTemp 集成：启用 ssrfConfig 时内网 URL 在请求前被拒绝
$rejected = false;
try {
    FileUtil::savePathToLocalTemp('http://127.0.0.1/x.jpg', 'jpg', false, [
        'ssrfConfig' => ['enable' => true],
    ]);
} catch (BizException $e) {
    $rejected = true;
}
TestCase::assertTrue($rejected, 'SSRF Biz: savePathToLocalTemp 启用 ssrfConfig 拒绝内网 URL');

TestCase::assertTrue(true, 'SSRF Biz: 完成');