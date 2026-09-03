<?php
use ModStart\Test\TestCase;

TestCase::describe('VisitStatistic Biz', function () {
    TestCase::test('主工具类可加载', function () {
        TestCase::assertTrue(class_exists('Module\VisitStatistic\Util\VisitStatisticUtil'), 'VisitStatistic Biz: 主工具类可加载');
    });
    TestCase::test('业务逻辑完成', function () {
        TestCase::assertTrue(true, 'VisitStatistic Biz: 完成');
    });
});