<?php
use ModStart\Test\TestCase;
use ModStart\Test\TestHttp;

TestCase::describe('AdminManager Api', function () {
    TestCase::test('无 API 路由跳过 HTTP 测试', function () {
        TestCase::assertTrue(true, 'AdminManager API HTTP: 无路由');
    });
});