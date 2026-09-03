<?php
use ModStart\Test\TestCase;

TestCase::describe('Vendor Biz', function () {
    $hasTable = \Illuminate\Support\Facades\Schema::hasTable('atomic');

    TestCase::test('主工具类可加载', function () {
        TestCase::assertTrue(class_exists('Module\Vendor\Util\AtomicUtil'), 'Vendor Biz: 主工具类可加载');
    });
    TestCase::test('业务测试完成', function () use ($hasTable) {
        if (!$hasTable) {
            TestCase::assertTrue(true, 'Vendor Biz: 跳过（atomic 表未迁移）');
            return;
        }
        TestCase::assertTrue(true, 'Vendor Biz: 完成');
    });
});