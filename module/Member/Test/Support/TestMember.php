<?php

namespace Module\Member\Test\Support;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use ModStart\Core\Dao\ModelUtil;

/**
 * 测试用 Member 用户认证工具
 *
 * 支持两种模式：
 * 1. 直接调用控制器模式（legacy）：loginAsTest() / login($id) → 操作 Session Facade
 * 2. HTTP 请求模式：loginAsTestHttp() / loginHttp($id) → 创建持久化 Session，返回 api-token
 */
class TestMember
{
    /** @var int|null 当前测试用户 ID（由 Member Seed 设置） */
    private static $testUserId = null;

    /** @var string|null HTTP 模式下的 api-token（= Session ID） */
    private static $testApiToken = null;

    /**
     * 设置测试用户 ID（由 Member Seed 调用）
     * @param int $userId
     */
    public static function setTestUserId($userId)
    {
        self::$testUserId = intval($userId);
    }

    /**
     * 获取测试用户 ID
     * @return int|null
     */
    public static function getTestUserId()
    {
        return self::$testUserId;
    }

    /**
     * 获取 HTTP 模式 api-token
     * @return string|null
     */
    public static function getTestApiToken()
    {
        return self::$testApiToken;
    }

    // =========================================================================
    // HTTP 模式：以 api-token 形式注入 Session，供 TestHttp 使用
    // =========================================================================

    /**
     * 以测试用户登录（HTTP 模式），返回 api-token（= session id）
     * @return string|null
     */
    public static function loginAsTestHttp()
    {
        $userId = self::$testUserId ?: 1;
        return self::loginHttp($userId);
    }

    /**
     * 以指定用户登录（HTTP 模式），返回 api-token
     * @param int $userId
     * @return string|null
     */
    public static function loginHttp($userId)
    {
        if (empty($userId)) {
            return null;
        }
        if (!Schema::hasTable('member_user')) {
            return null;
        }
        $user = ModelUtil::get('member_user', ['id' => intval($userId)]);
        if (empty($user)) {
            return null;
        }
        // 使用全局 session.store 单例（同一 PHP 进程中 HTTP kernel 共享同一个 handler）
        /** @var \Illuminate\Session\Store $session */
        $session = app('session.store');
        $session->start();
        $session->put('memberUserId', $user['id']);
        $session->put('_memberUser', $user);
        $session->save();
        self::$testApiToken = $session->getId();
        return self::$testApiToken;
    }

    /**
     * 清除 HTTP 模式 api-token
     */
    public static function clearTestApiToken()
    {
        self::$testApiToken = null;
    }

    // =========================================================================
    // 直接调用模式（legacy）：直接写 Session Facade，供直接调用 Controller 使用
    // =========================================================================

    /**
     * 以测试用户模拟登录（直接模式：设置 Session，不经过密码验证）
     * @return bool
     */
    public static function loginAsTest()
    {
        $userId = self::$testUserId ?: 1;
        return self::login($userId);
    }

    /**
     * 以指定用户 ID 模拟登录（直接模式）
     * @param int $memberUserId
     * @return bool
     */
    public static function login($memberUserId)
    {
        if (empty($memberUserId)) {
            return false;
        }
        if (!Schema::hasTable('member_user')) {
            return false;
        }
        $user = ModelUtil::get('member_user', ['id' => intval($memberUserId)]);
        if (empty($user)) {
            return false;
        }
        Session::put('memberUserId', $user['id']);
        Session::put('_memberUser', $user);
        return true;
    }

    /**
     * 退出登录（清除 Session 中的用户信息）
     */
    public static function logout()
    {
        Session::forget('memberUserId');
        Session::forget('_memberUser');
    }
}
