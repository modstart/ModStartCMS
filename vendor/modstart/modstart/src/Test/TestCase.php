<?php

namespace ModStart\Test;

/**
 * 测试断言工具类，供 Test/Api 和 Test/Biz 脚本使用
 */
class TestCase
{
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
}
