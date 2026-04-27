<?php
use ModStart\Test\TestCase;
use ModStart\Test\TestHttp;

// AdminManager: 无 API routes.php，跳过 HTTP 测试
TestCase::assertTrue(true, 'AdminManager API HTTP: 无路由');
