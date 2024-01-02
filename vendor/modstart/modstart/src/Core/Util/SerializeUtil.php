<?php


namespace ModStart\Core\Util;


class SerializeUtil
{
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
        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public static function jsonEncodeObject($data, $options = 0)
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_FORCE_OBJECT | $options);
    }

    public static function jsonEncode($data, $options = 0)
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | $options);
    }

    public static function jsonEncodePretty($data, $options = 0)
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | $options);
    }

    public static function jsonDecode($data)
    {
        return @json_decode($data, true);
    }

}
