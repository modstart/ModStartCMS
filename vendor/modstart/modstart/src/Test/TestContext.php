<?php

namespace ModStart\Test;

/**
 * 测试上下文，记录测试运行结果
 */
class TestContext
{
    private static $passed = 0;
    private static $failed = 0;
    private static $failures = [];
    private static $currentFile = null;

    public static function reset()
    {
        self::$passed = 0;
        self::$failed = 0;
        self::$failures = [];
        self::$currentFile = null;
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
