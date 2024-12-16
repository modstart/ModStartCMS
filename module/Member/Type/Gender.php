<?php

namespace Module\Member\Type;

use ModStart\Core\Type\BaseType;

class Gender implements BaseType
{
    const MALE = 1;
    const FEMALE = 2;
    const UNKNOWN = 0;

    public static function getList()
    {
        return [
            self::MALE => '男',
            self::FEMALE => '女',
            self::UNKNOWN => '未知',
        ];
    }

    public static function labelToValue($label)
    {
        switch ($label) {
            case 'male':
                return self::MALE;
            case 'female':
                return self::FEMALE;
        }
        return null;
    }

    public static function valueToLabel($value)
    {
        switch ($value) {
            case self::MALE:
                return 'male';
            case self::FEMALE:
                return 'female';
        }
        return null;
    }

}
