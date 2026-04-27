<?php
use ModStart\Test\TestCase;
use ModStart\Test\TestHttp;

if (!\Illuminate\Support\Facades\Schema::hasTable('banner')) {
    TestCase::assertTrue(true, 'Banner API: 跳过（banner 表未迁移）');
    return;
}
$ret = TestHttp::post('/api/banner/get');
TestCase::assertTrue(isset($ret['code']), 'Banner API: banner/get 返回合法响应');
