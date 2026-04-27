<?php
use ModStart\Test\TestCase;
use ModStart\Test\TestHttp;

if (!\Illuminate\Support\Facades\Schema::hasTable('article')) {
    TestCase::assertTrue(true, 'Article API: 跳过（article 表未迁移）');
    return;
}
$ret = TestHttp::post('/api/article/get');
TestCase::assertTrue(isset($ret['code']), 'Article API: article/get 返回合法响应');
