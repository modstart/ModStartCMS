<?php


namespace Module\Member\Type;


use ModStart\Core\Type\BaseType;

class MemberPasswordStrength implements BaseType
{
    const NO_LIMIT = 'no_limit';
    const STRENGTH_2 = 'strength_2';
    const STRENGTH_3 = 'strength_3';

    public static function getList()
    {
        return [
            self::NO_LIMIT => '不限制',
            self::STRENGTH_2 => '大写/小写/数字/符号 至少包含2种',
            self::STRENGTH_3 => '大写/小写/数字/符号 至少包含3种',
        ];
    }
}
