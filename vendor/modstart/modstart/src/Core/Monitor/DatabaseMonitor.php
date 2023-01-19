<?php


namespace ModStart\Core\Monitor;


use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use ModStart\Core\Util\ArrayUtil;

class DatabaseMonitor
{
    private static $queryCountPerRequest = 0;
    private static $queryCountPerRequestSqls = [];

    public static function init()
    {
        if (!config('modstart.trackPerformance', false)) {
            return;
        }
        self::$queryCountPerRequest = 0;
        self::$queryCountPerRequestSqls = [];
        try {
            DB::listen(function ($query, $bindings = null, $time = null, $connectionName = null) {
                self::$queryCountPerRequest++;
                $sql = $query;
                if (method_exists(\ModStart\ModStart::class, 'env')
                    && \ModStart\ModStart::env() == 'laravel9') {
                    /** @var QueryExecuted $query */
                    $sql = $query->sql;
                    $bindings = $query->bindings;
                    $time = $query->time;
                }
                self::$queryCountPerRequestSqls[] = [
                    'sql' => $sql,
                    'bindings' => $bindings,
                ];
                // Log::info("SQL $sql, " . json_encode($bindings));
                if ($time > 500) {
                    Log::warning("LONG_SQL ${time}ms, $sql, " . ArrayUtil::serializeForLog($bindings));
                }
            });
        } catch (\Exception $e) {
        }
    }

    public static function getQueryCountPerRequest()
    {
        return self::$queryCountPerRequest;
    }

    public static function getQueryCountPerRequestSqls()
    {
        foreach (self::$queryCountPerRequestSqls as $i => $v) {
            if (is_array($v)) {
                $bindings = ArrayUtil::serializeForLog($v['bindings']);
                self::$queryCountPerRequestSqls[$i] = $v['sql'] . ', ' . $bindings;
            }
        }
        return self::$queryCountPerRequestSqls;
    }

}
