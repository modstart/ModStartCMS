<?php

namespace ModStart\Admin\Type;

use ModStart\Core\Type\BaseType;

class AdminLogType implements BaseType
{
    const INFO = 1;
    const ERROR = 2;

    public static function getList()
    {
        return [
            self::INFO => L('Info'),
            self::ERROR => L('Error'),
        ];
    }
}
