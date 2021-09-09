<?php

namespace Module\Member\Type;

use ModStart\Core\Type\BaseType;

class MemberMoneyCashType implements BaseType
{
    const ALIPAY = 1;

    public static function getList()
    {
        return [
            self::ALIPAY => '支付宝',
        ];
    }

}