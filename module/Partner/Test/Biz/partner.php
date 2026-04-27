<?php
use ModStart\Test\TestCase;

TestCase::assertTrue(class_exists('Module\Partner\Util\PartnerUtil'), 'Partner Biz: 主工具类可加载');
if (!\Illuminate\Support\Facades\Schema::hasTable('partner')) {
    TestCase::assertTrue(true, 'Partner Biz: 跳过（partner 表未迁移）');
    return;
}
TestCase::assertTrue(true, 'Partner Biz: 完成');
