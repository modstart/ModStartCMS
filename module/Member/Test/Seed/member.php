<?php
/**
 * Member 模块 Seed 测试
 * 创建/更新一个测试用会员用户，供后续 API/Biz 测试使用
 */

use ModStart\Test\TestSeed;
use ModStart\Test\TestCase;
use Module\Member\Test\Support\TestMember;
use ModStart\Core\Dao\ModelUtil;

// 检查 member_user 表是否存在
if (!\Illuminate\Support\Facades\Schema::hasTable('member_user')) {
    TestCase::assertTrue(true, 'Member Seed: 跳过（member_user 表未迁移）');
    return;
}

// 填充/更新测试用户（AUTO_TEST=true 时生效）
TestSeed::upsert(
    'member_user',
    ['username' => '__test_user__'],
    [
        'phone'         => null,
        'email'         => 'test@test.local',
        'password'      => md5('test123'),
        'passwordSalt'  => '00000000',
        'avatar'        => '',
        'nickname'      => '测试用户',
    ],
    'Member Seed: 填充测试用户'
);

// 找到测试用户，设置到 TestMember 以供后续测试使用
$testUser = ModelUtil::get('member_user', ['username' => '__test_user__']);
if (empty($testUser)) {
    // 如果 AUTO_TEST 未开启，取第一个已存在用户
    $testUser = ModelUtil::get('member_user', []);
}

if (!empty($testUser)) {
    TestMember::setTestUserId($testUser['id']);
    TestCase::assertTrue(true, 'Member Seed: 测试用户已就绪 (id=' . $testUser['id'] . ')');
} else {
    TestCase::assertTrue(true, 'Member Seed: 无可用测试用户（跳过 Member auth 测试）');
}


// 检查 member_user 表是否存在
if (!\Illuminate\Support\Facades\Schema::hasTable('member_user')) {
    TestCase::assertTrue(true, 'Member Seed: 跳过（member_user 表未迁移）');
    return;
}

// 填充/更新测试用户（AUTO_TEST=true 时生效）
TestSeed::upsert(
    'member_user',
    ['username' => '__test_user__'],
    [
        'phone'         => null,
        'email'         => 'test@test.local',
        'password'      => md5('test123'),
        'passwordSalt'  => '00000000',
        'avatar'        => '',
        'nickname'      => '测试用户',
    ],
    'Member Seed: 填充测试用户'
);

// 找到测试用户，设置到 TestMember 以供后续测试使用
$testUser = ModelUtil::get('member_user', ['username' => '__test_user__']);
if (empty($testUser)) {
    // 如果 AUTO_TEST 未开启，取第一个已存在用户
    $testUser = ModelUtil::get('member_user', []);
}

if (!empty($testUser)) {
    TestMember::setTestUserId($testUser['id']);
    TestCase::assertTrue(true, 'Member Seed: 测试用户已就绪 (id=' . $testUser['id'] . ')');
} else {
    TestCase::assertTrue(true, 'Member Seed: 无可用测试用户（跳过 Member auth 测试）');
}
