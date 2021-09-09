<?php


namespace ModStart\Core\Dao;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ModelManageUtil
{
    public static function table($table, $conn = 'mysql')
    {
        return config('database.connections.' . $conn . '.prefix') . $table;
    }

    public static function fixDatetime0000($table, $field)
    {
        if (!is_array($field)) {
            $field = [$field];
        }
        $now = Carbon::now();
        $table = ModelManageUtil::table($table);
        foreach ($field as $item) {
            ModelManageUtil::statement("UPDATE $table SET `$item`='$now' WHERE `$item`='0000-00-00 00:00:00'");
        }
    }

    public static function hasTable($table)
    {
        return Schema::hasTable($table);
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
        $results = DB::select(DB::raw($sql));
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

}
