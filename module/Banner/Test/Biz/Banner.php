<?php
use ModStart\Test\TestCase;

TestCase::describe('Banner Biz', function () {
    TestCase::test('主工具类可加载', function () {
        TestCase::assertTrue(class_exists('Module\Banner\Util\BannerUtil'), 'Banner Biz: 主工具类可加载');
    });
    $hasTable = \Illuminate\Support\Facades\Schema::hasTable('banner');
    TestCase::test('完成', function () use ($hasTable) {
        if (!$hasTable) {
            TestCase::assertTrue(true, 'Banner Biz: 跳过（banner 表未迁移）');
            return;
        }
        TestCase::assertTrue(true, 'Banner Biz: 完成');
    });
});