<?php


namespace Module\Member\Type;


use ModStart\Core\Type\BaseType;

class MemberStatus implements BaseType
{
    const NORMAL = 1;
    const FORBIDDEN = 2;

    public static function getList()
    {
        return [
            self::NORMAL => '正常',
            self::FORBIDDEN => '禁用',
        ];
    }

}
