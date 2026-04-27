<?php
use ModStart\Test\TestCase;
use ModStart\Test\TestHttp;

$ret = TestHttp::post('/api/captcha_tecmz/info');
TestCase::assertSuccess($ret, 'CaptchaTecmz API: captcha_tecmz/info');
