<?php
use ModStart\Test\TestCase;

TestCase::assertTrue(class_exists('Module\Banner\Util\BannerUtil'), 'Banner Biz: 主工具类可加载');
if (!\Illuminate\Support\Facades\Schema::hasTable('banner')) {
    TestCase::assertTrue(true, 'Banner Biz: 跳过（banner 表未迁移）');
    return;
}
TestCase::assertTrue(true, 'Banner Biz: 完成');
