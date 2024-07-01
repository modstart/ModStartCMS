<?php

namespace ModStart\Core\Util;

class ValueUtil
{
    public static function value($value)
    {
        if (is_callable($value)) {
            return $value();
        }
        return $value;
    }
}
