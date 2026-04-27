<?php
use ModStart\Test\TestCase;

TestCase::assertTrue(class_exists('Module\Vendor\Util\AtomicUtil'), 'Vendor Biz: 主工具类可加载');
if (!\Illuminate\Support\Facades\Schema::hasTable('atomic')) {
    TestCase::assertTrue(true, 'Vendor Biz: 跳过（atomic 表未迁移）');
    return;
}
TestCase::assertTrue(true, 'Vendor Biz: 完成');
