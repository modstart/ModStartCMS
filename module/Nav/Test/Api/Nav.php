<?php
use ModStart\Test\TestCase;
use ModStart\Test\TestHttp;

TestCase::describe('Nav Api', function () {
    TestCase::test('无独立 HTTP 路由跳过', function () {
        // Nav: 无 API routes.php，跳过 HTTP 测试
        TestCase::assertTrue(true, 'Nav API HTTP: 无路由');
    });
});