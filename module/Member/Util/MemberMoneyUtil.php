<?php

namespace Module\Member\Util;

use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Util\IdUtil;
use ModStart\Core\Util\SerializeUtil;
use Module\Member\Model\MemberMoney;
use Module\Member\Model\MemberMoneyLog;
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
     * 变更用户的余额
     * !!! 这个方法应该在事务中调用 !!!
     *
     * @param $memberUserId int 用户ID
     * @param $change string 变化值，正数为增加，负数为减少
     * @param $remark string 备注
     * @param $meta array|null 元数据
     * @throws BizException
     */
    public static function change($memberUserId, $change, $remark, $meta = null)
    {
        BizException::throwsIf('MemberMoneyUtil.change.change=0', !$change);
        $m = ModelUtil::getWithLock(MemberMoney::class, [
            'memberUserId' => $memberUserId,
        ]);
        if (empty($m)) {
            $m = ModelUtil::insert(MemberMoney::class, [
                'memberUserId' => $memberUserId, 'total' => 0,
            ]);
        }
        $total = bcadd($m['total'], $change, 2);
        BizException::throwsIf('MemberMoneyUtil.change.total<0', $change < 0 && $total < 0);
        if ($meta && !is_string($meta)) {
            $meta = SerializeUtil::jsonEncode($meta);
        }
        ModelUtil::insert(MemberMoneyLog::class, [
            'memberUserId' => $memberUserId,
            'change' => $change,
            'remark' => $remark,
            'meta' => $meta,
        ]);
        ModelUtil::update(MemberMoney::class, $m['id'], [
            'total' => $total,
        ]);
    }

    /**
     * 用户提现
     * !!! 这个方法应该在事务中调用 !!!
     *
     * @param $memberUserId int 用户ID
     * @param $money float 提现金额
     * @param $moneyAfterTax float 扣税后的金额
     * @param $type string 提现方式
     * @param $realname string 真实姓名
     * @param $account string 账号
     * @param string $remark 备注
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
