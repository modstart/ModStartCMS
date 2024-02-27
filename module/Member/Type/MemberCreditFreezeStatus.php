<?php

namespace Module\Member\Type;

use ModStart\Core\Type\BaseType;

class MemberCreditFreezeStatus implements BaseType
{
    const PROCESSING = 1;
    const COMMITTED = 2;
    const CANCELED = 3;

    public static function getList()
    {
        return [
            self::PROCESSING => '冻结中',
            self::COMMITTED => '已提交',
            self::CANCELED => '已取消',
        ];
    }

}
