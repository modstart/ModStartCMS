<?php


namespace Module\Member\Core;


use ModStart\Core\Dao\ModelUtil;
use Module\Member\Util\MemberMoneyUtil;
use Module\PayCenter\Biz\AbstractPayCenterBiz;
use Module\PayCenter\Type\PayType;
use Module\Vendor\Type\OrderStatus;

class MemberMoneyChargePayCenterBiz extends AbstractPayCenterBiz
{
    const NAME = 'mMemberMoneyCharge';

    public function name()
    {
        return self::NAME;
    }

    public function title()
    {
        return '用户钱包充值';
    }

    public function disabledPayTypes()
    {
        return [
            PayType::MEMBER_MONEY,
        ];
    }


    public function onPayed($payBizId, $payOrder, $param = [])
    {
        $order = ModelUtil::get('member_money_charge_order', $payBizId);
        if ($order['status'] !== OrderStatus::WAIT_PAY) {
            return;
        }
        ModelUtil::update('member_money_charge_order', $payBizId, ['status' => OrderStatus::COMPLETED]);
        MemberMoneyUtil::change($order['memberUserId'], $order['money'], '钱包充值');
    }


}
