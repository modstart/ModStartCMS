<?php

namespace ModStart\Core\Exception;


class BizException extends \Exception
{
    public static function throws($msg)
    {
        throw new BizException($msg);
    }

    public static function throwsIf($msg, $condition)
    {
        if ($condition) {
            throw new BizException($msg);
        }
    }

    public static function throwsIfResponseError($response)
    {
        if (empty($response)) {
            throw new BizException('Response Empty');
        }
        if ($response['code']) {
            throw new BizException($response['msg']);
        }
    }

    public static function throwsIfEmpty($msg, $object)
    {
        if (empty($object)) {
            throw new BizException($msg);
        }
    }

    public static function throwsIfNotEmpty($msg, $object)
    {
        if (!empty($object)) {
            throw new BizException($msg);
        }
    }

}
