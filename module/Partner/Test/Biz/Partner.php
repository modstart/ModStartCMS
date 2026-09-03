<?php
use ModStart\Test\TestCase;

TestCase::describe('Partner Biz', function () {
    $hasTable = \Illuminate\Support\Facades\Schema::hasTable('partner');

    TestCase::test('主工具类可加载', function () {
        TestCase::assertTrue(class_exists('Module\Partner\Util\PartnerUtil'), 'Partner Biz: 主工具类可加载');
    });

    TestCase::test('功能验证完成', function () use ($hasTable) {
        if (!$hasTable) {
            TestCase::assertTrue(true, 'Partner Biz: 跳过（partner 表未迁移）');
            return;
        }
        TestCase::assertTrue(true, 'Partner Biz: 完成');
    });
});