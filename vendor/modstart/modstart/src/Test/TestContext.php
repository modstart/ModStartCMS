<?php

namespace ModStart\Test;

/**
 * 测试上下文，记录测试运行结果
 */
class TestContext
{
    private static $passed = 0;
    private static $failed = 0;
    private static $skipped = 0;
    private static $failures = [];
    private static $currentFile = null;
    private static $testFilter = null;

    public static function reset()
    {
        self::$passed = 0;
        self::$failed = 0;
        self::$skipped = 0;
        self::$failures = [];
        self::$currentFile = null;
        self::$testFilter = null;
    }

    /**
     * 设置测试用例过滤关键字（为空表示不过滤）
     *
     * @param string|null $filter
     */
    public static function setTestFilter($filter)
    {
        $filter = null === $filter ? null : trim((string)$filter);
        self::$testFilter = ('' === $filter) ? null : $filter;
    }

    /**
     * 获取测试用例过滤关键字
     *
     * @return string|null
     */
    public static function getTestFilter()
    {
        return self::$testFilter;
    }

    /**
     * 判断测试用例名称是否匹配过滤关键字
     *
     * @param string $name
     * @return bool
     */
    public static function matchTestFilter($name)
    {
        if (null === self::$testFilter) {
            return true;
        }
        return stripos((string)$name, self::$testFilter) !== false;
    }

    /**
     * 记录跳过的测试用例
     *
     * @param string $name
     */
    public static function skip($name)
    {
        self::$skipped++;
    }

    public static function getSkipped()
    {
        return self::$skipped;
    }

    public static function setCurrentFile($file)
    {
        self::$currentFile = $file;
    }

    public static function pass($name)
    {
        self::$passed++;
    }

    public static function fail($name, $reason = '')
    {
        self::$failed++;
        self::$failures[] = [
            'file' => self::$currentFile,
            'name' => $name,
            'reason' => $reason,
        ];
    }

    public static function getPassed()
    {
        return self::$passed;
    }

    public static function getFailed()
    {
        return self::$failed;
    }

    public static function getFailures()
    {
        return self::$failures;
    }

    public static function hasFailure()
    {
        return self::$failed > 0;
    }
}
