<?php


namespace ModStart\Core\Util;


class SerializeUtil
{
    public static function jsonObject($data)
    {
        if (empty($data)) {
            $data = new \stdClass();
        }
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}
