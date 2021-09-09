<?php

namespace Module\Member\Type;

use ModStart\Core\Type\BaseType;

class MemberMoneyChargeStatus implements BaseType
{
    const CREATED = 1;
    const SUCCESS = 2;

    public static function getList()
    {
        return [
            self::CREATED => '新创建',
            self::SUCCESS => '提现成功',
        ];
    }

}