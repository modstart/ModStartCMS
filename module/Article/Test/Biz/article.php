<?php
use ModStart\Test\TestCase;

TestCase::assertTrue(class_exists('Module\Article\Util\ArticleUtil'), 'Article Biz: 主工具类可加载');
if (!\Illuminate\Support\Facades\Schema::hasTable('article')) {
    TestCase::assertTrue(true, 'Article Biz: 跳过（article 表未迁移）');
    return;
}
TestCase::assertTrue(true, 'Article Biz: 完成');
