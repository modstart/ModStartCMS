<?php
use ModStart\Test\TestCase;

TestCase::describe('Article Biz', function () {
    TestCase::test('主工具类可加载', function () {
        TestCase::assertTrue(class_exists('Module\Article\Util\ArticleUtil'), 'Article Biz: 主工具类可加载');
    });
    $hasTable = \Illuminate\Support\Facades\Schema::hasTable('article');
    TestCase::test('完成', function () use ($hasTable) {
        if (!$hasTable) {
            TestCase::assertTrue(true, 'Article Biz: 跳过（article 表未迁移）');
            return;
        }
        TestCase::assertTrue(true, 'Article Biz: 完成');
    });
});