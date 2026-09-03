<?php
use ModStart\Test\TestCase;
use ModStart\Test\TestHttp;

TestCase::describe('CaptchaTecmz Api', function () {
    TestCase::test('验证码信息接口可访问', function () {
        $ret = TestHttp::post('/api/captcha_tecmz/info');
        TestCase::assertSuccess($ret, 'CaptchaTecmz API: captcha_tecmz/info');
    });
});