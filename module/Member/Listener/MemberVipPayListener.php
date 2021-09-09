<?php

namespace Module\Member\Listener;

use ModStart\Core\Dao\ModelUtil;
use Module\Member\Constant\PayConstant;
use Module\Member\Util\MemberUtil;
use Module\Member\Util\MemberVipUtil;
use Module\PayCenter\Events\OrderPayedEvent;
use Module\Vendor\Type\OrderStatus;

class MemberVipPayListener
{
    public function onOrderPayed(OrderPayedEvent $event)
    {
        $biz = $event->biz;
        $bizId = $event->bizId;

        switch ($biz) {
            case PayConstant::MEMBER_VIP:
                $order = ModelUtil::get('member_vip_order', $bizId);
                if (empty($order)) {
                    return;
                }
                $memberUser = MemberUtil::get($order['memberUserId']);
                $memberVip = MemberVipUtil::get($order['vipId']);
                ModelUtil::update('member_vip_order', ['id' => $bizId], ['status' => OrderStatus::COMPLETED]);
                $update = [];
                $update['vipId'] = $order['vipId'];
                $update['vipExpire'] = $order['expire'];
                MemberUtil::update($order['memberUserId'], $update);
                break;
        }

    }

    public function subscribe($events)
    {
        $events->listen(
            OrderPayedEvent::class,
            '\Module\Member\Listener\MemberVipPayListener@onOrderPayed'
        );
    }
}