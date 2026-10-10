<?php
use ModStart\Test\TestCase;
use ModStart\Test\TestHttp;

TestCase::describe('Member Api', function () {
    $hasTable = \Illuminate\Support\Facades\Schema::hasTable('member_user');

    TestCase::test('公开接口可访问', function () use ($hasTable) {
        if (!$hasTable) {
            TestCase::assertTrue(true, '跳过（member_user 表未迁移）');
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
    });

    TestCase::test('登录用户接口正常', function () use ($hasTable) {
        if (!$hasTable) {
            TestCase::assertTrue(true, '跳过（member_user 表未迁移）');
            return;
        }
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

        // [T-android-member-auth] 当前用户信息：登录态应返回会员 id 与基础字段
        $ret = TestHttp::post('/api/member/current');
        TestCase::assertSuccess($ret, 'Member API: member/current 返回成功');
        TestCase::assertTrue(isset($ret['data']['id']) && $ret['data']['id'] > 0, 'Member API: member/current 返回会员 id');
        TestCase::assertTrue(isset($ret['data']['username']), 'Member API: member/current 返回 username');

        // 未登录（无 token）应被拒绝而非 500
        TestHttp::clearToken();
        $ret = TestHttp::post('/api/member/current');
        TestCase::assertError($ret, 'Member API: member/current 未登录拒绝');
        TestHttp::useToken($token);

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
    });

    TestCase::test('头像 SSRF 防护', function () use ($hasTable) {
        if (!$hasTable) {
            TestCase::assertTrue(true, '跳过（member_user 表未迁移）');
            return;
        }
        $token = \Module\Member\Test\Support\TestMember::loginAsTestHttp();
        if (empty($token)) {
            TestCase::assertTrue(true, 'Member API: 跳过（无测试用户）');
            return;
        }
        TestHttp::useToken($token);

        // 头像 SSRF 防护：内网/保留地址应被拒绝（type 可任意伪造进入 default 分支）
        $ret = TestHttp::post('/api/member_profile/avatar', ['type' => 'ssrf', 'avatar' => 'http://127.0.0.1/x.jpg']);
        TestCase::assertError($ret, 'Member API: avatar 拒绝内网 127.0.0.1');
        $ret = TestHttp::post('/api/member_profile/avatar', ['type' => 'ssrf', 'avatar' => 'http://169.254.169.254/latest/meta-data/']);
        TestCase::assertError($ret, 'Member API: avatar 拒绝云元数据 169.254.169.254');
        $ret = TestHttp::post('/api/member_profile/avatar', ['type' => 'ssrf', 'avatar' => 'http://10.0.0.1/x.jpg']);
        TestCase::assertError($ret, 'Member API: avatar 拒绝内网 10.x');
        $ret = TestHttp::post('/api/member_profile/avatar', ['type' => 'ssrf', 'avatar' => 'http://172.16.0.1/x.jpg']);
        TestCase::assertError($ret, 'Member API: avatar 拒绝内网 172.16.x');
        $ret = TestHttp::post('/api/member_profile/avatar', ['type' => 'ssrf', 'avatar' => 'http://192.168.1.1/x.jpg']);
        TestCase::assertError($ret, 'Member API: avatar 拒绝内网 192.168.x');
        $ret = TestHttp::post('/api/member_profile/avatar', ['type' => 'ssrf', 'avatar' => 'http://localhost/x.jpg']);
        TestCase::assertError($ret, 'Member API: avatar 拒绝 localhost');

        TestHttp::clearToken();
    });
});