<?php

namespace Module\Survey\Type;


use ModStart\Core\Type\BaseType;

class JoinType implements BaseType
{
    const NO_LIMIT = 1;
    const ONCE_PER_USER = 2;

    public static function getList()
    {
        return [
            self::NO_LIMIT => '不限制',
            self::ONCE_PER_USER => '每个用户一次',
        ];
    }

}
