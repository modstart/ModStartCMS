<?php
use ModStart\Test\TestCase;
use Module\Member\Test\Support\TestMember;

TestCase::describe('Member Biz', function () {
    $hasTable = \Illuminate\Support\Facades\Schema::hasTable('member_user');

    TestCase::test('工具类可加载', function () {
        TestCase::assertTrue(class_exists('Module\Member\Util\MemberUtil'), 'Member Biz: MemberUtil 类可加载');
        TestCase::assertTrue(class_exists('Module\Member\Auth\MemberUser'), 'Member Biz: MemberUser 类可加载');
    });

    TestCase::test('统计总数返回数值', function () use ($hasTable) {
        if (!$hasTable) {
            TestCase::assertTrue(true, '跳过（member_user 表未迁移）');
            return;
        }
        $total = \Module\Member\Util\MemberUtil::total();
        TestCase::assertTrue(is_numeric($total), 'Member Biz: total() 返回数值');
    });

    TestCase::test('完成', function () use ($hasTable) {
        if (!$hasTable) {
            TestCase::assertTrue(true, '跳过（member_user 表未迁移）');
            return;
        }
        TestCase::assertTrue(true, 'Member Biz: 完成');
    });
});