<?php

namespace Module\Member\Util;

use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Util\IdUtil;
use Module\Member\Type\MemberMoneyCashStatus;
use Module\Member\Type\MemberMoneyChargeStatus;

class MemberMoneyUtil
{
    public static function paginateLog($memberUserId, $page, $pageSize, $option = [])
    {
        $option['where']['memberUserId'] = $memberUserId;
        $option['order'] = ['id', 'desc'];
        return ModelUtil::paginate('member_money_log', $page, $pageSize, $option);
    }

    public static function getTotal($memberUserId)
    {
        $m = ModelUtil::get('member_money', ['memberUserId' => $memberUserId]);
        if (empty($m)) {
            return '0.00';
        }
        return $m['total'];
    }

    /**
     * !! 这个方法应该在事务中调用
     *
     * @param $memberUserId
     * @param $change
     * @param $remark
     * @throws \Exception
     */
    public static function change($memberUserId, $change, $remark)
    {
        if (!$change) {
            BizException::throws('Member.MoneyChangeUtil -> change empty');
        }
        $m = ModelUtil::getWithLock('member_money', ['memberUserId' => $memberUserId]);
        if (empty($m)) {
            $m = ModelUtil::insert('member_money', ['memberUserId' => $memberUserId, 'total' => 0,]);
        }
        if ($change < 0 && $m['total'] + $change < 0) {
            BizException::throws('Member.MoneyChangeUtil -> total change to empty');
        }
        ModelUtil::insert('member_money_log', ['memberUserId' => $memberUserId, 'change' => $change, 'remark' => $remark]);
        $m = ModelUtil::update('member_money', ['id' => $m['id']], ['total' => $m['total'] + $change]);
        if ($m['total'] < 0) {
            BizException::throws('Member.MoneyChangeUtil -> total empty');
        }
    }

    /**
     * !! 这个方法应该在事务中调用
     *
     * @param $memberUserId
     * @param $money
     * @param $moneyAfterTax
     * @param $type
     * @param $realname
     * @param $account
     * @param string $remark
     * @throws \Exception
     */
    public static function cash($memberUserId, $money, $moneyAfterTax, $type, $realname, $account, $remark = '余额提现')
    {
        self::change($memberUserId, -$money, '余额提现');
        ModelUtil::insert('member_money_cash', [
            'memberUserId' => $memberUserId,
            'status' => MemberMoneyCashStatus::VERIFYING,
            'money' => $money,
            'moneyAfterTax' => $moneyAfterTax,
            'type' => $type,
            'realname' => $realname,
            'account' => $account,
            'remark' => $remark,
        ]);
    }

    public static function paginateCash($memberUserId, $page, $pageSize, $option = [])
    {
        $option['where']['memberUserId'] = $memberUserId;
        return ModelUtil::paginate('member_money_cash', $page, $pageSize, $option);
    }

    public static function createCharge($memberUserId, $fee)
    {
        return ModelUtil::insert('member_money_charge', [
            'sn' => IdUtil::generateSN(),
            'status' => MemberMoneyChargeStatus::CREATED,
            'memberUserId' => $memberUserId,
            'fee' => $fee,
        ]);
    }

    public static function processCharge($chargeId)
    {
        $charge = ModelUtil::getWithLock('member_money_charge', ['id' => $chargeId]);
        if (empty($charge)) {
            throw new \Exception('member_money_charge empty -> ' . $chargeId);
        }
        if ($charge['status'] != MemberMoneyChargeStatus::CREATED) {
            throw new \Exception('member_money_charge status error -> ' . $chargeId);
        }
        self::change($charge['memberUserId'], $charge['fee'], '充值');
        ModelUtil::update('member_money_charge', ['id' => $chargeId], [
            'status' => MemberMoneyChargeStatus::SUCCESS,
        ]);
    }
}
