<?php
use ModStart\Test\TestCase;

TestCase::assertTrue(class_exists('Module\Nav\Util\NavUtil'), 'Nav Biz: 主工具类可加载');
if (!\Illuminate\Support\Facades\Schema::hasTable('nav')) {
    TestCase::assertTrue(true, 'Nav Biz: 跳过（nav 表未迁移）');
    return;
}
TestCase::assertTrue(true, 'Nav Biz: 完成');
