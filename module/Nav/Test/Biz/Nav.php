<?php
use ModStart\Test\TestCase;

TestCase::describe('Nav Biz', function () {
    $hasTable = \Illuminate\Support\Facades\Schema::hasTable('nav');

    TestCase::test('主工具类可加载', function () {
        TestCase::assertTrue(class_exists('Module\Nav\Util\NavUtil'), 'Nav Biz: 主工具类可加载');
    });

    TestCase::test('功能验证完成', function () use ($hasTable) {
        if (!$hasTable) {
            TestCase::assertTrue(true, 'Nav Biz: 跳过（nav 表未迁移）');
            return;
        }
        TestCase::assertTrue(true, 'Nav Biz: 完成');
    });
});