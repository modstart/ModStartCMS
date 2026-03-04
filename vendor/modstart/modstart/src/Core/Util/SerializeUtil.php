<?php


namespace ModStart\Core\Util;


use Illuminate\Support\Facades\Log;

/**
 * @Util 序列化工具
 */
class SerializeUtil
{

    private static function utf8ize($mixed)
    {
        if (is_array($mixed)) {
            foreach ($mixed as $key => $value) {
                $mixed[$key] = self::utf8ize($value);
            }
        } elseif (is_string($mixed)) {
            if (!mb_check_encoding($mixed, 'UTF-8')) {
                $base64 = base64_encode($mixed);
                $mixed = 'base64:' . $base64;
            }
        }
        return $mixed;
    }

    private static function safeJsonEncode($data, $options)
    {
        $value = json_encode($data, $options);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $data = self::utf8ize($data);
            $error = json_encode([
                'error' => json_last_error_msg(),
                'data' => $data,
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            Log::error('SerializeUtil.jsonEncode.error - ' . $error);
            $value = json_encode($data, $options);
        }
        return $value;
    }

    /**
     * @param $data
     * @return false|string
     * @deprecated delete at 2024-04-26
     */
    public static function jsonObject($data)
    {
        if (empty($data)) {
            $data = new \stdClass();
        }
        return self::safeJsonEncode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * @Util 将数据编码为 JSON 对象属性（强制输出对象格式）
     * @param $data mixed
     * @return string
     */
    public static function jsonEncodeObject($data, $options = 0)
    {
        return self::safeJsonEncode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_FORCE_OBJECT | $options);
    }

    /**
     * @Util 将数据编码为 JSON 字符串（不转义中文和断杠符）
     * @param $data mixed
     * @param $options int JSON 选项
     * @return string
     */
    public static function jsonEncode($data, $options = 0)
    {
        return self::safeJsonEncode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | $options);
    }

    /**
     * @Util 将数据编码为美化格式的 JSON 字符串
     * @param $data mixed
     * @param $options int JSON 选项
     * @return string
     */
    public static function jsonEncodePretty($data, $options = 0)
    {
        return self::safeJsonEncode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | $options);
    }

    /**
     * @Util 将 JSON 字符串解码为数组
     * @param $data string JSON 字符串
     * @return array|null
     */
    public static function jsonDecode($data)
    {
        return @json_decode($data, true);
    }

    /**
     * @Util 将数组转换为对象（空数组返回空对象）
     * @param $array mixed
     * @return object
     */
    public static function objectArray($array)
    {
        if (empty($array)) {
            return new \stdClass();
        }
        return $array;
    }

}
