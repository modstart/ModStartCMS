<?php
use ModStart\Test\TestCase;
use ModStart\Test\TestHttp;

if (!\Illuminate\Support\Facades\Schema::hasTable('atomic')) {
    TestCase::assertTrue(true, 'Vendor API: 跳过（atomic 表未迁移）');
    return;
}
TestCase::assertTrue(true, 'Vendor API: captcha/image 跳过（图片接口）');
$ret = TestHttp::post('/api/entry/biz');
TestCase::assertSuccess($ret, 'Vendor API: entry/biz');
