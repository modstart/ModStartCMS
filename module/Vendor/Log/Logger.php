<?php

namespace Module\Vendor\Log;


class Logger
{
    private static function write($file, $type, $label, $msg)
    {
        if (!is_string($msg)) {
            $msg = json_encode($msg, JSON_UNESCAPED_UNICODE);
        }
        $string = "[" . sprintf('%05d', getmypid()) . "] " . date('Y-m-d H:i:s') . " - $label" . ($msg ? " - $msg" : '');
        @file_put_contents(storage_path("logs/${file}_${type}_" . date('Ymd') . ".log"), $string . "\n", FILE_APPEND);
        return $string;
    }

    public static function info($file, $label, $msg = '')
    {
        return self::write($file, 'info', $label, $msg);
    }

    public static function error($file, $label, $msg = '')
    {
        return self::write($file, 'error', $label, $msg);
    }
}