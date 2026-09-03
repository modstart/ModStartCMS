<?php
use ModStart\Test\TestCase;
use ModStart\Test\TestHttp;

TestCase::describe('SiteCounter Api', function () {
    // SiteCounter: 无 API routes.php，跳过 HTTP 测试
    TestCase::test('无 API 路由，跳过 HTTP 测试', function () {
        TestCase::assertTrue(true, 'SiteCounter API HTTP: 无路由');
    });
});