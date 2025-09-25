<?php

namespace ModStart\Core\Util;

use Illuminate\Support\Facades\Log;

class LogUtil
{
    private static function prepareLogData($data)
    {
        foreach ($data as $k => $v) {
            if (is_array($v)) {
                $data[$k] = self::prepareLogData($v);
            } else if (is_string($v)) {
                // like base64
                if (strlen($v) > 100 && preg_match('/^[A-Za-z0-9\/\r\n+]*={0,2}$/', substr($v, 0, 100))) {
                    $data[$k] = substr($v, 0, 100) . '...(len=' . strlen($v) . ')';
                } else {
                    $data[$k] = $v;
                }
            } else {
                $data[$k] = $v;
            }
        }
        return $data;
    }

    private static function buildString($label, $data = null)
    {
        $text = [];
        $text[] = $label;
        if (null !== $data) {
            if (is_string($data) || is_numeric($data)) {
                $text[] = $data;
            } else {
                $text[] = SerializeUtil::jsonEncode(self::prepareLogData($data));
            }
        }
        return join(' - ', $text);
    }

    public static function info($label, $data = null)
    {
        Log::info(self::buildString($label, $data));
    }

    public static function error($label, $data = null)
    {
        Log::error(self::buildString($label, $data));
    }

    public static function echoInfo($label, $data = null)
    {
        echo self::buildString($label, $data) . PHP_EOL;
    }
}
