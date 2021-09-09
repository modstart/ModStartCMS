<?php

namespace ModStart\Core\Exception;


use ModStart\Core\Input\Response;

class ResultException extends \Exception
{
    public static function throwsIfFail($ret)
    {
        if (Response::isError($ret)) {
            throw new ResultException($ret['msg']);
        }
    }

    public static function throwsIf($msg, $condition)
    {
        if ($condition) {
            throw new ResultException($msg);
        }
    }

}