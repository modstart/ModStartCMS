<?php

namespace ModStart\Test;

/**
 * 测试断言与测试组工具类，供 Test/Api、Test/Biz、Test/UI 脚本使用
 *
 * 支持两种用法：
 * 1. 直接断言（简单模式）
 *   TestCase::assertTrue($value, '说明');
 *
 * 2. 分组测试（推荐）：
 *   TestCase::describe('模块名', function () {
 *       TestCase::beforeEach(function () {
 *           // 每个 test 前执行
 *       });
 *       TestCase::test('测试功能1', function () {
 *           TestCase::assertTrue(true, '断言');
 *       });
 *       TestCase::test('测试功能2', function () {
 *           // ...
 *       });
 *   });
 *   describe 会按顺序执行组内的 beforeEach 与 test。
 */
class TestCase
{
    /**
     * 当前测试组名称
     * @var string|null
     */
    private static $currentGroup = null;

    /**
     * 当前测试组内注册的 beforeEach 回调
     * @var \Closure[]
     */
    private static $beforeEach = [];

    /**
     * 断言为真
     * @param bool $value
     * @param string $name
     */
    public static function assertTrue($value, $name = 'assertTrue')
    {
        if ($value) {
            TestContext::pass($name);
        } else {
            TestContext::fail($name, 'Expected true, got false');
        }
    }

    /**
     * 断言为假
     * @param bool $value
     * @param string $name
     */
    public static function assertFalse($value, $name = 'assertFalse')
    {
        if (!$value) {
            TestContext::pass($name);
        } else {
            TestContext::fail($name, 'Expected false, got true');
        }
    }

    /**
     * 断言相等
     * @param mixed $expected
     * @param mixed $actual
     * @param string $name
     */
    public static function assertEquals($expected, $actual, $name = 'assertEquals')
    {
        if ($expected === $actual) {
            TestContext::pass($name);
        } else {
            TestContext::fail($name, "Expected " . json_encode($expected) . ", got " . json_encode($actual));
        }
    }

    /**
     * 断言严格相等（===）
     * @param mixed $expected
     * @param mixed $actual
     * @param string $name
     */
    public static function assertSame($expected, $actual, $name = 'assertSame')
    {
        if ($expected === $actual) {
            TestContext::pass($name);
        } else {
            TestContext::fail($name, "Expected " . json_encode($expected) . ", got " . json_encode($actual));
        }
    }

    /**
     * 断言不为空
     * @param mixed $value
     * @param string $name
     */
    public static function assertNotEmpty($value, $name = 'assertNotEmpty')
    {
        if (!empty($value)) {
            TestContext::pass($name);
        } else {
            TestContext::fail($name, 'Expected not empty, got empty');
        }
    }

    /**
     * 断言为空
     * @param mixed $value
     * @param string $name
     */
    public static function assertEmpty($value, $name = 'assertEmpty')
    {
        if (empty($value)) {
            TestContext::pass($name);
        } else {
            TestContext::fail($name, 'Expected empty, got: ' . json_encode($value));
        }
    }

    /**
     * 断言接口返回成功
     * @param array $ret
     * @param string $name
     */
    public static function assertSuccess($ret, $name = 'assertSuccess')
    {
        if (isset($ret['code']) && $ret['code'] === 0) {
            TestContext::pass($name);
        } else {
            $msg = isset($ret['msg']) ? $ret['msg'] : json_encode($ret);
            TestContext::fail($name, 'Expected success response, got: ' . $msg);
        }
    }

    /**
     * 断言接口返回错误
     * @param array $ret
     * @param string $name
     */
    public static function assertError($ret, $name = 'assertError')
    {
        if (!isset($ret['code']) || $ret['code'] !== 0) {
            TestContext::pass($name);
        } else {
            $msg = isset($ret['msg']) ? $ret['msg'] : json_encode($ret);
            TestContext::fail($name, 'Expected error response, got success: ' . $msg);
        }
    }

    /**
     * 断言数组包含指定键
     * @param string $key
     * @param array $array
     * @param string $name
     */
    public static function assertArrayHasKey($key, $array, $name = 'assertArrayHasKey')
    {
        if (is_array($array) && array_key_exists($key, $array)) {
            TestContext::pass($name);
        } else {
            TestContext::fail($name, "Array does not have key: $key");
        }
    }

    /**
     * 断言字符串包含子串
     * @param string $needle
     * @param string $haystack
     * @param string $name
     */
    public static function assertContains($needle, $haystack, $name = 'assertContains')
    {
        if (is_string($haystack) && strpos($haystack, $needle) !== false) {
            TestContext::pass($name);
        } else {
            TestContext::fail($name, "String does not contain: $needle");
        }
    }

    /**
     * 断言 HTTP 响应无 500 错误
     * @param int $statusCode
     * @param string $name
     */
    public static function assertHttpOk($statusCode, $name = 'assertHttpOk')
    {
        if (intval($statusCode) >= 200 && intval($statusCode) < 400) {
            TestContext::pass($name);
        } else {
            TestContext::fail($name, "HTTP status $statusCode is not OK (2xx/3xx)");
        }
    }

    /**
     * 开启一个测试组。describe 内注册的 beforeEach 与 test 会立即按顺序执行。
     * 回调可声明一个参数接收 TestCase 实例：function (TestCase $testCase) { ... }
     * @param string $name 测试组名称
     * @param \Closure $callback 组内注册 beforeEach / test 的回调
     */
    public static function describe($name, $callback)
    {
        $prevGroup = self::$currentGroup;
        $prevBeforeEach = self::$beforeEach;
        self::$currentGroup = $name;
        self::$beforeEach = [];
        try {
            call_user_func($callback, new static());
            // describe 内注册的 test 在注册时立即执行（见 self::test）
        } catch (\Exception $e) {
            TestContext::fail($name, 'describe error: ' . $e->getMessage());
        }
        self::$currentGroup = $prevGroup;
        self::$beforeEach = $prevBeforeEach;
    }

    /**
     * 注册 beforeEach 钩子，仅对当前 describe 组内的 test 生效。
     * 每组只允许注册一次；重复注册会追加执行。
     * 回调可声明一个参数接收 TestCase 实例。
     * @param \Closure $callback
     */
    public static function beforeEach($callback)
    {
        self::$beforeEach[] = $callback;
    }

    /**
     * 注册并立即执行一个测试用例（需在 describe 内调用）。
     * 执行前先运行组内注册的 beforeEach 钩子。
     * 回调可声明一个参数接收 TestCase 实例。
     * @param string $name 测试用例名称
     * @param \Closure $callback 测试逻辑
     */
    public static function test($name, $callback)
    {
        $fullName = self::$currentGroup ? (self::$currentGroup . ' -> ' . $name) : $name;
        try {
            foreach (self::$beforeEach as $hook) {
                call_user_func($hook, new static());
            }
            call_user_func($callback, new static());
        } catch (\Exception $e) {
            TestContext::fail($fullName, $e->getMessage());
        }
    }

    /**
     * 获取当前测试组名称
     * @return string|null
     */
    public static function currentGroup()
    {
        return self::$currentGroup;
    }
}