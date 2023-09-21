<?php


namespace ModStart\Core\Dao;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\StrUtil;

class ModelManageUtil
{
    public static function tablePrefix($conn = 'mysql')
    {
        return config('database.connections.' . $conn . '.prefix');
    }

    public static function table($table, $conn = 'mysql')
    {
        return self::tablePrefix($conn) . $table;
    }

    public static function fixDatetime0000($table, $field)
    {
        if (!is_array($field)) {
            $field = [$field];
        }
        $now = Carbon::now();
        $table = ModelManageUtil::table($table);
        foreach ($field as $item) {
            ModelManageUtil::statement("UPDATE $table SET `$item`='$now' WHERE `$item`='1970-01-01 00:00:00'");
        }
    }

    public static function tableComment($table, $comment, $conn = 'mysql')
    {
        $table = self::table($table, $conn);
        self::statement("ALTER TABLE `$table` COMMENT = '$comment'", $conn);
    }

    public static function tablesComment($tableCommentMap, $conn = 'mysql')
    {
        foreach ($tableCommentMap as $table => $comment) {
            self::tableComment($table, $comment, $conn);
        }
    }

    public static function listTables($conn = 'mysql')
    {
        $results = self::query('SHOW TABLES', $conn);
        $tables = [];
        $tablePrefix = self::tablePrefix($conn);
        foreach ($results as $result) {
            $values = array_values($result);
            $table = $values[0];
            if (!empty($tablePrefix)) {
                if (!starts_with($table, $tablePrefix)) {
                    continue;
                }
                $table = substr($table, strlen($tablePrefix));
            }
            $tables[] = $table;
        }
        return $tables;
    }

    public static function hasTable($table)
    {
        return Schema::hasTable($table);
    }

    public static function dropTable($table)
    {
        return Schema::dropIfExists($table);
    }

    public static function renameTable($tableFrom, $tableTo)
    {
        Schema::rename($tableFrom, $tableTo);
    }

    public static function listTableColumns($table)
    {
        if (!self::hasTable($table)) {
            return [];
        }
        return Schema::getColumnListing($table);
    }

    public static function hasTableColumn($table, $column)
    {
        $columns = self::listTableColumns($table);
        return in_array($column, $columns);
    }

    public static function listIds($table, $where = [], $limit = 100, $idKey = 'id')
    {
        $records = ModelUtil::model($table)->where($where)->limit($limit)->get([$idKey]);
        return ArrayUtil::fetchSpecifiedKeyToArray($records, $idKey);
    }

    public static function statement($sql, $conn = 'mysql')
    {
        DB::connection($conn)->statement($sql);
    }

    public static function truncate($table, $conn = 'mysql')
    {
        $table = self::table($table, $conn);
        self::statement("TRUNCATE TABLE `$table`", $conn);
    }

    public static function query($sql, $conn = 'mysql')
    {
        $results = DB::connection($conn)->select(DB::raw($sql));
        if (is_array($results)) {
            foreach ($results as $k => $result) {
                if ($result instanceof \stdClass) {
                    $results[$k] = (array)$result;
                }
            }
        } else if ($results instanceof \stdClass) {
            $results = (array)$results;
        }
        return $results;
    }

    public static function moveToConn($table, $conn, $limit = 100, $idKey = 'id')
    {
        $ids = ModelManageUtil::listIds($table, [], $limit, $idKey);
        if (empty($ids)) {
            return 0;
        }
        $records = ModelUtil::allIn($table, $idKey, $ids);
        if (empty($records)) {
            return 0;
        }
        try {
            DModelUtil::insertAll($conn, $table, $records, false);
            ModelUtil::deleteIn($table, $ids, $idKey);
            return count($records);
        } catch (\Exception $e) {
            if (!Str::contains($e->getMessage(), 'Duplicate entry')) {
                return 0;
            }
            $imported = 0;
            foreach ($records as $record) {
                try {
                    DModelUtil::insertAll($conn, $table, [$record], false);
                    ModelUtil::delete($table, [$idKey => $record[$idKey]]);
                    $imported++;
                } catch (\Exception $e) {
                    if (Str::contains($e->getMessage(), 'Duplicate entry')) {
                        try {
                            DModelUtil::delete($conn, $table, [$idKey => $record[$idKey]]);
                            DModelUtil::insertAll($conn, $table, [$record], false);
                            ModelUtil::delete($table, [$idKey => $record[$idKey]]);
                            $imported++;
                        } catch (\Exception $e) {
                        }
                    }
                }
            }
            return $imported;
        }
    }

    /**
     * @param $table
     * @param \Closure $schemaCallback
     * @param string $connection
     * @since 1.7.0
     */
    public static function migrate($table, \Closure $schemaCallback, $connection = 'mysql')
    {
        $schemaCallback($table, Schema::connection($connection));
    }

    /**
     * @param $fieldType
     * @return bool
     * @since 1.7.0
     */
    public static function ddlFieldTypeIsCorrect($fieldType)
    {
        if (preg_match('/^INT$/', $fieldType)) {
            return true;
        }
        if (preg_match('/^DECIMAL\\(\\d+,\\d+\\)$/', $fieldType)) {
            return true;
        }
        if (preg_match('/^DATE$/', $fieldType)) {
            return true;
        }
        if (preg_match('/^DATETIME$/', $fieldType)) {
            return true;
        }
        if (preg_match('/^TIME/', $fieldType)) {
            return true;
        }
        if (preg_match('/^VARCHAR\\(\\d+\\)$/', $fieldType)) {
            return true;
        }
        if (preg_match('/^TEXT$/', $fieldType)) {
            return true;
        }
        return false;
    }

    /**
     * @param $table
     * @param $fieldName
     * @param $fieldType
     * @throws \Exception
     * @since 1.7.0
     */
    public static function ddlFieldAdd($table, $fieldName, $fieldType)
    {
        if (!preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $table)) {
            throw new \Exception('DDL table error');
        }
        if (!preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $fieldName)) {
            throw new \Exception('DDL fieldName error');
        }
        if (!self::ddlFieldTypeIsCorrect($fieldType)) {
            throw new \Exception('DDL fieldType error : ' . $fieldType);
        }
        $table = self::table($table);
        $sql = "ALTER TABLE `$table` ADD `$fieldName` $fieldType DEFAULT NULL";
        self::statement($sql);
    }

    /**
     * @param $table
     * @param $fieldNameOld
     * @param $fieldName
     * @param $fieldType
     * @throws \Exception
     * @since 1.7.0
     */
    public static function ddlFieldChange($table, $fieldNameOld, $fieldName, $fieldType)
    {
        if (!preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $table)) {
            throw new \Exception('DDL table error');
        }
        if (!preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $fieldNameOld)) {
            throw new \Exception('DDL fieldNameOld error');
        }
        if (!preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $fieldName)) {
            throw new \Exception('DDL fieldName error');
        }
        if (!self::ddlFieldTypeIsCorrect($fieldType)) {
            throw new \Exception('DDL fieldType error : ' . $fieldType);
        }
        $table = self::table($table);
        $sql = "ALTER TABLE `$table` CHANGE `$fieldNameOld` `$fieldName` $fieldType DEFAULT NULL";
        self::statement($sql);
    }

    /**
     * @param $table
     * @param $fieldName
     * @param $fieldType
     * @throws \Exception
     * @since 1.7.0
     */
    public static function ddlFieldModify($table, $fieldName, $fieldType)
    {
        if (!preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $table)) {
            throw new \Exception('DDL table error');
        }
        if (!preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $fieldName)) {
            throw new \Exception('DDL fieldName error');
        }
        if (!self::ddlFieldTypeIsCorrect($fieldType)) {
            throw new \Exception('DDL fieldType error : ' . $fieldType);
        }
        $table = self::table($table);
        $sql = "ALTER TABLE `$table` MODIFY `$fieldName` $fieldType DEFAULT NULL";
        self::statement($sql);
    }

    /**
     * @param $table
     * @param $fieldName
     * @throws \Exception
     * @since 1.7.0
     */
    public static function ddlFieldDrop($table, $fieldName)
    {
        if (!preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $table)) {
            throw new \Exception('DDL table error');
        }
        if (!preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $fieldName)) {
            throw new \Exception('DDL fieldName error');
        }
        $table = self::table($table);
        $sql = "ALTER TABLE `$table` DROP `$fieldName`";
        self::statement($sql);
    }

    public static function tableStructure($table, $conn = 'mysql')
    {
        $tablePrefixed = self::table($table, $conn);
        $result = self::query("SHOW CREATE TABLE `$tablePrefixed`", $conn);
        $result = isset($result[0]['Create Table']) ? $result[0]['Create Table'] : '';
        BizException::throwsIfEmpty('ShowTableStructureError', $result);
        $result = str_replace("`$tablePrefixed`", "`__table_prefix__$table`", $result);
        return $result;
    }

    public static function fields($table, $conn = 'mysql')
    {
        return Cache::remember('ModelManageUtil:fields:' . $table, 60, function () use ($table, $conn) {
            $tablePrefixed = self::table($table, $conn);
            $result = self::query("SHOW COLUMNS FROM `$tablePrefixed`", $conn);
            $records = [];
            foreach ($result as $r) {
                $records[] = [
                    'field' => $r['Field'],
                    'type' => strtoupper($r['Type']),
                    'null' => $r['Null'],
                    'key' => $r['Key'],
                    'default' => $r['Default'],
                    'extra' => $r['Extra'],
                ];
            }
            return $records;
        });
    }

    public static function field($table, $field, $conn = 'mysql')
    {
        foreach (self::fields($table, $conn) as $item) {
            if ($item['field'] == $field) {
                return $item;
            }
        }
        return null;
    }

    public static function fieldValueCheckOrFail($label, $value, $table, $field, $conn = 'mysql')
    {
        $ret = self::fieldValueCheck($value, $table, $field, $conn);
        if (Response::isError($ret)) {
            BizException::throws($label . $ret['msg']);
        }
    }

    /**
     * 判断字段值是否合法
     * @param $value
     * @param $table
     * @param $field
     * @param string $conn
     */
    public static function fieldValueCheck($value, $table, $field, $conn = 'mysql')
    {
        $f = self::field($table, $field, $conn);
        if (empty($f)) {
            return Response::generateError("字段不存在");
        }
        // print_r($f);
        if ('TEXT' == $f['type']) {
            // 最多存储 65535 个字节
            if (strlen($value) > 65535) {
                return Response::generateError("长度不能超过 65535");
            }
        } else if (preg_match('/^VARCHAR\((\d+)\)$/', $f['type'], $m)) {
            // 最多存储 $m[1] 个字节
            if (StrUtil::mbLength($value) > $m[1]) {
                return Response::generateError("长度不能超过 {$m[1]}");
            }
        }
        return Response::generateSuccess();
    }

    public static function databaseStructure($conn = 'mysql',
                                             $buildInTableComment = [],
                                             $buildColumnComment = []
    )
    {
        $buildColumnComment = array_merge([
            'id' => 'ID',
            'created_at' => L('Created At'),
            'updated_at' => L('Updated At'),
        ], $buildColumnComment);
        $tables = self::query('SHOW TABLE STATUS;', $conn);
        $result = [];
        foreach ($tables as $item) {
            $tableName = $item['Name'];
            $one = [];
            $one['table'] = [
                'name' => $tableName,
                'comment' => $item['Comment'],
            ];
            if (empty($one['table']['comment'])) {
                if (isset($buildInTableComment[$tableName])) {
                    $one['table']['comment'] = $buildInTableComment[$tableName];
                }
            }
            $columns = self::query("SHOW FULL FIELDS FROM `" . $one['table']['name'] . "`;", $conn);
            $one['columns'] = [];
            foreach ($columns as $column) {
                $c = [
                    'name' => $column['Field'],
                    'type' => strtoupper($column['Type']),
                    'comment' => $column['Comment'],
                ];
                if (empty($c['comment'])) {
                    $map = [
                        'id' => 'ID',
                        'created_at' => L('Created At'),
                        'updated_at' => L('Updated At'),
                    ];
                    $c['comment'] = isset($buildColumnComment[$c['name']]) ? $buildColumnComment[$c['name']] : '';
                }
                $one['columns'][] = $c;
            }
            $result[] = $one;
        }
        return $result;
    }

    public static function databaseStructureMarkdown($conn = 'mysql')
    {
        $result = self::databaseStructure($conn);
        $markdown = [];
        foreach ($result as $r) {
            $markdown[] = "## {$r['table']['name']} {$r['table']['comment']}";
            $markdown[] = '';
            $markdown[] = '';
            $markdown[] = '| ' . join(' | ', ['字段', '类型', '说明']) . ' |';
            $markdown[] = '| ' . join(' | ', ['---', '---', '---']) . ' |';
            foreach ($r['columns'] as $c) {
                $markdown[] = '| ' . join(' | ', [$c['name'], $c['type'], $c['comment'] ? $c['comment'] : '-']) . ' |';
            }
            $markdown[] = '';
            $markdown[] = '';
        }
        return join("\n", $markdown);
    }

}
