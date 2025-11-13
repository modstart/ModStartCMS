<?php

namespace Module\Member\Type;

use ModStart\Core\Type\BaseType;

class MemberMoneyCashType implements BaseType
{
    const ALIPAY = 1;
    const WECHAT = 2;
    const BANK = 3;

    public static function getList()
    {
        return [
            self::ALIPAY => '支付宝',
            self::WECHAT => '微信',
            self::BANK => '银行卡',
        ];
    }

}
