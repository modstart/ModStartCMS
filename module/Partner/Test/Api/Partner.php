<?php
use ModStart\Test\TestCase;
use ModStart\Test\TestHttp;

TestCase::describe('Partner Api', function () {
    TestCase::test('无独立 HTTP 路由跳过', function () {
        // Partner: 无 API routes.php，跳过 HTTP 测试
        TestCase::assertTrue(true, 'Partner API HTTP: 无路由');
    });
});