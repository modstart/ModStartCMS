<?php
use ModStart\Test\TestCase;
use ModStart\Test\TestHttp;

TestCase::describe('AigcBase Api', function () {
    TestCase::test('aigc_base/file 已移除（不再返回 500）', function () {
        $ret = TestHttp::post('/api/aigc_base/file');
        TestCase::assertTrue(isset($ret['code']) && $ret['code'] == -1, 'AigcBase API: aigc_base/file 已移除');
        TestCase::assertTrue(strpos($ret['msg'], 'Not Found') !== false, 'AigcBase API: 返回 Api Not Found');
    });
});
