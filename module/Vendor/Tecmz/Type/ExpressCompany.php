<?php

namespace Module\Vendor\Tecmz\Type;

use ModStart\Core\Type\BaseType;

class ExpressCompany implements BaseType
{
    public static function getList()
    {
        return [
            'SF' => '顺丰速运',
            'HTKY' => '百世快递',
            'ZTO' => '中通快递',
            'STO' => '申通快递',
            'YTO' => '圆通速递',
            'YD' => '韵达速递',
            'YZPY' => '邮政快递包裹',
            'EMS' => 'EMS',
            'HHTT' => '天天快递',
            'JD' => '京东快递',
            'UC' => '优速快递',
            'DBL' => '德邦快递',
            'ZJS' => '宅急送',
        ];
    }

}
