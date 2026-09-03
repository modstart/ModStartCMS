<?php
use ModStart\Test\TestCase;

TestCase::describe('AigcBase Api', function () {
    TestCase::test('aigc_base/file 跳过（需外部 ApiKey）', function () {
        TestCase::assertTrue(true, 'AigcBase API: aigc_base/file 跳过（需要外部 ApiKey 配置）');
    });
});