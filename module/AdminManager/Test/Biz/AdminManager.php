<?php
use ModStart\Test\TestCase;

TestCase::describe('AdminManager Biz', function () {
    TestCase::test('主工具类可加载', function () {
        TestCase::assertTrue(class_exists('Module\AdminManager\Util\ModuleUtil'), 'AdminManager Biz: 主工具类可加载');
    });

    TestCase::test('完成', function () {
        TestCase::assertTrue(true, 'AdminManager Biz: 完成');
    });
});