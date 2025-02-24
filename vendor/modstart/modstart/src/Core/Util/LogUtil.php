<?php

namespace ModStart\Core\Util;

use Illuminate\Support\Facades\Log;

class LogUtil
{
    private static function buildString($label, $data = null)
    {
        $text = [];
        $text[] = $label;
        if (null !== $data) {
            if (is_string($data) || is_numeric($data)) {
                $text[] = $data;
            } else {
                $text[] = SerializeUtil::jsonEncode($data);
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
}
