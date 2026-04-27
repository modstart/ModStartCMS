<?php
use ModStart\Test\TestCase;
use ModStart\Test\TestHttp;

if (!\Illuminate\Support\Facades\Schema::hasTable('member_user')) {
    TestCase::assertTrue(true, 'Member API: 跳过（member_user 表未迁移）');
    return;
}

// 无需登录的公开接口
TestCase::assertTrue(true, 'Member API: login 跳过（写操作）');
TestCase::assertTrue(true, 'Member API: logout 跳过（写操作）');
TestCase::assertTrue(true, 'Member API: login_captcha 跳过（验证码）');
TestCase::assertTrue(true, 'Member API: register 跳过（写操作）');
TestCase::assertTrue(true, 'Member API: oauth/login 跳过（第三方登录）');
$ret = TestHttp::post('/api/member_doc/get');
TestCase::assertTrue(isset($ret['code']), 'Member API: member_doc/get 返回合法响应');

// 需要登录的接口
$token = \Module\Member\Test\Support\TestMember::loginAsTestHttp();
if (empty($token)) {
    TestCase::assertTrue(true, 'Member API: 跳过（无测试用户）');
    return;
}
TestHttp::useToken($token);

TestCase::assertTrue(true, 'Member API: member_profile/password 跳过（写操作）');
TestCase::assertTrue(true, 'Member API: member_profile/delete_info 跳过（写操作）');
TestCase::assertTrue(true, 'Member API: member_profile/nickname 跳过（写操作）');
TestCase::assertTrue(true, 'Member API: member_message/read_all 跳过（写操作）');

$ret = TestHttp::post('/api/member_message');
TestCase::assertSuccess($ret, 'Member API: member_message paginate');

$ret = TestHttp::post('/api/member_vip/get');
TestCase::assertTrue(isset($ret['code']), 'Member API: member_vip/get 返回合法响应');

$ret = TestHttp::post('/api/member_vip/info');
TestCase::assertSuccess($ret, 'Member API: member_vip/info');

$ret = TestHttp::post('/api/member_vip/all');
TestCase::assertSuccess($ret, 'Member API: member_vip/all');

$ret = TestHttp::post('/api/member_address/all');
TestCase::assertSuccess($ret, 'Member API: member_address/all');

$ret = TestHttp::post('/api/member_address/get_default');
TestCase::assertTrue(isset($ret['code']), 'Member API: member_address/get_default 返回合法响应');

$ret = TestHttp::post('/api/member_money/get');
TestCase::assertTrue(isset($ret['code']), 'Member API: member_money/get 返回合法响应');

$ret = TestHttp::post('/api/member_money/log');
TestCase::assertSuccess($ret, 'Member API: member_money/log');

$ret = TestHttp::post('/api/member_money/cash/get');
TestCase::assertTrue(isset($ret['code']), 'Member API: member_money/cash/get 返回合法响应');

$ret = TestHttp::post('/api/member_money/cash/log');
TestCase::assertSuccess($ret, 'Member API: member_money/cash/log');

$ret = TestHttp::post('/api/member_credit/get');
TestCase::assertTrue(isset($ret['code']), 'Member API: member_credit/get 返回合法响应');

$ret = TestHttp::post('/api/member_credit/log');
TestCase::assertSuccess($ret, 'Member API: member_credit/log');

TestHttp::clearToken();
