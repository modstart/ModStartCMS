<?php

namespace Module\Member\Type;

use ModStart\Core\Type\BaseType;

class MemberMoneyCashStatus implements BaseType
{
    const VERIFYING = 1;
    const SUCCESS = 2;

    public static function getList()
    {
        return [
            self::VERIFYING => '正在审核',
            self::SUCCESS => '提现成功',
        ];
    }

}