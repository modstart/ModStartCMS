<?php

namespace Module\Member\Type;

use ModStart\Core\Type\BaseType;

class Education implements BaseType
{
    const PRIMARY_SCHOOL = 1;
    const JUNIOR_HIGH_SCHOOL = 2;
    const SENIOR_HIGH_SCHOOL = 3;
    const TECHNICAL_SECONDARY_SCHOOL = 4;
    const JUNIOR_COLLEGE = 5;
    const BACHELOR = 6;
    const MASTER = 7;
    const DOCTOR = 8;

    public static function getList()
    {
        return [
            self::PRIMARY_SCHOOL => '小学',
            self::JUNIOR_HIGH_SCHOOL => '初中',
            self::SENIOR_HIGH_SCHOOL => '高中',
            self::TECHNICAL_SECONDARY_SCHOOL => '中专',
            self::JUNIOR_COLLEGE => '大专',
            self::BACHELOR => '本科',
            self::MASTER => '硕士',
            self::DOCTOR => '博士',
        ];
    }


}