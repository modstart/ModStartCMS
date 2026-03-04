<?php

namespace ModStart\Core\Util;

use Illuminate\Support\Facades\Log;

/**
 * @Util 日志工具
 */
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

    /**
     * @Util 输出 info 级日志
     * @param $label string 日志标签
     * @param $data mixed|数据 附加数据，为 null 时不输出
     * @return void
     */
    public static function info($label, $data = null)
    {
        Log::info(self::buildString($label, $data));
    }

    /**
     * @Util 输出 error 级日志
     * @param $label string 日志标签
     * @param $data mixed 附加数据，为 null 时不输出
     * @return void
     */
    public static function error($label, $data = null)
    {
        Log::error(self::buildString($label, $data));
    }

    /**
     * @Util 直接将 info 级日志输出到控制台
     * @param $label string 日志标签
     * @param $data mixed 附加数据，为 null 时不输出
     * @return void
     */
    public static function echoInfo($label, $data = null)
    {
        echo self::buildString($label, $data) . PHP_EOL;
    }
}
