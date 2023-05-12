<?php


namespace Module\Member\Core;


use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\Response;
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

    public function createOrderForQuick($quickOrder, $param = [])
    {
        BizException::throwsIf('钱包充值未开启', !modstart_config('Member_MoneyChargeEnable', false));
        $memberUserId = $quickOrder['session']['memberUserId'];
        $money = $quickOrder['param']['money'];
        BizException::throwsIfEmpty('用户ID为空', $memberUserId);
        BizException::throwsIf('充值金额异常', $money < 0.01 || $money > 1000 * 10000);
        $order = ModelUtil::insert('member_money_charge_order', [
            'status' => OrderStatus::WAIT_PAY,
            'memberUserId' => $memberUserId,
            'money' => $money,
        ]);
        return Response::generateSuccessData([
            'bizId' => $order['id'],
            'feeTotal' => $money,
            'body' => '钱包充值',
            'param' => [],
            'redirect' => modstart_web_url('member_money'),
        ]);
    }


}
