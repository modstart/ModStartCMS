<?php


namespace ModStart\Core\Monitor;


use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DatabaseMonitor
{
    private static $queryCountPerRequest = 0;
    private static $queryCountPerRequestSqls = [];

    public static function init()
    {
        self::$queryCountPerRequest = 0;
        self::$queryCountPerRequestSqls = [];

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
            self::$queryCountPerRequestSqls[] = "$sql, " . json_encode($bindings);
            // Log::info("SQL $sql, " . json_encode($bindings));
            if ($time > 500) {
                $param = json_encode($bindings);
                Log::warning("LONG_SQL ${time}ms, $sql, $param");
            }
        });
    }

    public static function getQueryCountPerRequest()
    {
        return self::$queryCountPerRequest;
    }

    public static function getQueryCountPerRequestSqls()
    {
        return self::$queryCountPerRequestSqls;
    }

}
