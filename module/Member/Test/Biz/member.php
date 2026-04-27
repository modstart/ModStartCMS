<?php
use ModStart\Test\TestCase;
use Module\Member\Test\Support\TestMember;

TestCase::assertTrue(class_exists('Module\Member\Util\MemberUtil'), 'Member Biz: MemberUtil 类可加载');
TestCase::assertTrue(class_exists('Module\Member\Auth\MemberUser'), 'Member Biz: MemberUser 类可加载');

if (!\Illuminate\Support\Facades\Schema::hasTable('member_user')) {
    TestCase::assertTrue(true, 'Member Biz: 跳过（member_user 表未迁移）');
    return;
}

$total = \Module\Member\Util\MemberUtil::total();
TestCase::assertTrue(is_numeric($total), 'Member Biz: total() 返回数值');

TestCase::assertTrue(true, 'Member Biz: 完成');
