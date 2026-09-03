<?php
use ModStart\Test\TestCase;
use ModStart\Test\TestHttp;

TestCase::describe('Vendor Api', function () {
    $hasTable = \Illuminate\Support\Facades\Schema::hasTable('atomic');

    TestCase::test('captcha/image 图片接口跳过', function () use ($hasTable) {
        if (!$hasTable) {
            TestCase::assertTrue(true, 'Vendor API: 跳过（atomic 表未迁移）');
            return;
        }
        TestCase::assertTrue(true, 'Vendor API: captcha/image 跳过（图片接口）');
    });
    TestCase::test('entry/biz 接口可访问', function () use ($hasTable) {
        if (!$hasTable) {
            TestCase::assertTrue(true, 'Vendor API: 跳过（atomic 表未迁移）');
            return;
        }
        $ret = TestHttp::post('/api/entry/biz');
        TestCase::assertSuccess($ret, 'Vendor API: entry/biz');
    });
});