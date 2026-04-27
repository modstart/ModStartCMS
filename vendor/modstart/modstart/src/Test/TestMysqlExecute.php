<?php

namespace ModStart\Test;

/**
 * MySQL 语句直接执行工具，供测试脚本调试数据使用
 *
 * 用法示例：
 *   $rows = TestMysqlExecute::query('SELECT * FROM member_user LIMIT 5');
 *   $row  = TestMysqlExecute::first('SELECT * FROM ad WHERE id = 1');
 *   $n    = TestMysqlExecute::count('SELECT COUNT(*) AS n FROM member_user');
 *   TestMysqlExecute::dump('member_user');
 *   TestMysqlExecute::execute("UPDATE member_user SET nickname='test' WHERE id=1");
 */
class TestMysqlExecute
{
    /**
     * 执行 SELECT 语句，返回所有结果行（每行为关联数组）
     *
     * @param string $sql 完整的 SQL 语句
     * @return array
     */
    public static function query($sql)
    {
        $rows = \DB::select(\DB::raw($sql));
        return array_map(function ($row) {
            return (array)$row;
        }, $rows);
    }

    /**
     * 执行 SELECT 语句，返回第一行（关联数组），无结果返回 null
     *
     * @param string $sql
     * @return array|null
     */
    public static function first($sql)
    {
        $rows = \DB::select(\DB::raw($sql));
        return !empty($rows) ? (array)$rows[0] : null;
    }

    /**
     * 执行 SELECT COUNT(*) AS n ... 形式的语句，返回整数计数
     *
     * @param string $sql
     * @return int
     */
    public static function count($sql)
    {
        $row = self::first($sql);
        if (empty($row)) {
            return 0;
        }
        $val = isset($row['n']) ? $row['n'] : array_values($row)[0];
        return (int)$val;
    }

    /**
     * 执行 INSERT / UPDATE / DELETE / DDL 语句，返回是否成功
     *
     * @param string $sql
     * @return bool
     */
    public static function execute($sql)
    {
        return \DB::statement(\DB::raw($sql));
    }

    /**
     * 查询指定表的前 N 条数据并打印，方便调试
     *
     * @param string $table 表名
     * @param int    $limit 返回行数，默认 10
     * @return array
     */
    public static function dump($table, $limit = 10)
    {
        $limit = (int)$limit;
        $rows  = self::query("SELECT * FROM `$table` LIMIT $limit");
        if (empty($rows)) {
            echo "[TestMysqlExecute::dump] $table: (无数据)\n";
            return $rows;
        }
        echo "[TestMysqlExecute::dump] $table (前 $limit 行):\n";
        foreach ($rows as $i => $row) {
            echo "  [$i] " . json_encode($row, JSON_UNESCAPED_UNICODE) . "\n";
        }
        return $rows;
    }

    /**
     * 获取某表的行数
     *
     * @param string $table 表名
     * @return int
     */
    public static function tableCount($table)
    {
        return self::count("SELECT COUNT(*) AS n FROM `$table`");
    }

    /**
     * 检查某条件是否存在记录
     *
     * @param string $table 表名
     * @param string $where WHERE 子句（不含 WHERE 关键字），如 "id=1 AND status=1"
     * @return bool
     */
    public static function exists($table, $where)
    {
        $n = self::count("SELECT COUNT(*) AS n FROM `$table` WHERE $where");
        return $n > 0;
    }
}
