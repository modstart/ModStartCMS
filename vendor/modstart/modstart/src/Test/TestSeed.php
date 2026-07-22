<?php

namespace ModStart\Test;

use ModStart\Core\Dao\ModelUtil;

/**
 * 测试数据填充工具类，供 Test/Seed 脚本使用
 * 必须在 env('AUTO_TEST') 为真时才真正写入，否则直接跳过
 */
class TestSeed
{
    /**
     * 填充或更新一条测试数据
     * 如果 where 条件匹配到记录，则更新为 data；否则插入 where+data 合并后的数据。
     * 仅在 AUTO_TEST=true 时执行。
     *
     * @param string $table  数据库表名（小写下划线）
     * @param array  $where  匹配条件
     * @param array  $data   需要设置的字段值
     * @param string $name   描述（用于日志输出）
     * @return bool
     */
    public static function upsert($table, $where, $data, $name = '')
    {
        if (!env('AUTO_TEST')) {
            return true;
        }
        $existing = ModelUtil::get($table, $where);
        if ($existing) {
            ModelUtil::update($table, $where, $data);
        } else {
            ModelUtil::insert($table, array_merge($where, $data));
        }
        return true;
    }

    /**
     * 如果不存在则插入测试数据
     *
     * @param string $table
     * @param array  $where
     * @param array  $data
     * @return bool
     */
    public static function insertIfNotExists($table, $where, $data = [])
    {
        if (!env('AUTO_TEST')) {
            return true;
        }
        ModelUtil::insertIfNotExists($table, $where, $data);
        return true;
    }

    /**
     * 检查数据库表是否存在，不存在时直接跳过（输出提示）
     * 用于 Seed 文件开头，省去重复的 if + return 样板代码
     *
     * @param string $table 表名
     * @param string $name  描述（可选，默认用表名）
     * @return bool 表存在返回 true，不存在返回 false
     */
    public static function ensureTable($table, $name = '')
    {
        if (empty($name)) {
            $name = $table;
        }
        if (!\Illuminate\Support\Facades\Schema::hasTable($table)) {
            \ModStart\Test\TestCase::assertTrue(true, $name . ' Seed: 跳过（' . $table . ' 表未迁移）');
            return false;
        }
        return true;
    }

    /**
     * 获取当前时间字符串，格式 Y-m-d H:i:s
     * 用于 Seed 文件中替代 $now = date('Y-m-d H:i:s')
     *
     * @return string
     */
    public static function now()
    {
        return date('Y-m-d H:i:s');
    }

    /**
     * 统计数据库中满足条件的记录数
     * 用于 Seed 文件中替代 ModelUtil::count(...)
     *
     * @param string $table 表名或 Model 类名
     * @param array  $where 查询条件
     * @return int
     */
    public static function count($table, $where = [])
    {
        return \ModStart\Core\Dao\ModelUtil::count($table, $where);
    }
}
