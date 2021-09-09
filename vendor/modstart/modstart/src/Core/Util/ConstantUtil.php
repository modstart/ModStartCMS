<?php

namespace ModStart\Core\Util;


class ConstantUtil
{
    public static function dump($cls)
    {
        return (new \ReflectionClass($cls))->getConstants();
    }
}