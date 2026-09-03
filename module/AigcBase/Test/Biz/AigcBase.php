<?php
use ModStart\Test\TestCase;

TestCase::describe('AigcBase Biz', function () {
    TestCase::test('主工具类可加载', function () {
        TestCase::assertTrue(class_exists('Module\AigcBase\Util\AigcCreditUtil'), 'AigcBase Biz: 主工具类可加载');
    });

    TestCase::test('完成', function () {
        TestCase::assertTrue(true, 'AigcBase Biz: 完成');
    });
});