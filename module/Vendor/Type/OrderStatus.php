<?php

namespace Module\Vendor\Type;

use ModStart\Core\Type\BaseType;

class OrderStatus implements BaseType
{
    const WAIT_PAY = 1;
    const WAIT_SHIPPING = 2;
    const WAIT_CONFIRM = 3;

    const COMPLETED = 50;

    // 订单过期，支付成功的订单
    const CANCEL_PAID = 97;
    const CANCEL_EXPIRED = 98;
    const CANCEL = 99;

    // const CANCEL_QUEUE = 100;

    public static function getList()
    {
        return [
            self::WAIT_PAY => '待付款',
            self::WAIT_SHIPPING => '待发货',
            self::WAIT_CONFIRM => '待收货',

            self::COMPLETED => '已完成',

            self::CANCEL_PAID => '支付成功取消',
            self::CANCEL_EXPIRED => '订单过期取消',
            self::CANCEL => '订单取消',
            // self::CANCEL_QUEUE => '正在取消',
        ];
    }

    public static function filterList($types)
    {
        $filtered = [];
        foreach (self::getList() as $k => $v) {
            if (!in_array($k, $types)) {
                continue;
            }
            $filtered[$k] = $v;
        }
        return $filtered;
    }

    public static function simple()
    {
        return self::filterList([
            self::WAIT_PAY,
            self::COMPLETED,
            self::CANCEL_PAID,
            self::CANCEL_EXPIRED,
            self::CANCEL,
        ]);
    }
}
