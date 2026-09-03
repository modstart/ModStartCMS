<?php
use ModStart\Test\TestCase;
use ModStart\Test\TestHttp;

// VisitStatistic: 无 API routes.php，跳过 HTTP 测试
TestCase::describe('VisitStatistic Api', function () {
    TestCase::test('无 API 路由跳过 HTTP 测试', function () {
        TestCase::assertTrue(true, 'VisitStatistic API HTTP: 无路由');
    });
});