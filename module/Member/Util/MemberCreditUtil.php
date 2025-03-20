<?php

namespace Module\Member\Util;

use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Util\SerializeUtil;
use Module\Member\Auth\MemberUser;
use Module\Member\Model\MemberCredit;
use Module\Member\Model\MemberCreditFreeze;
use Module\Member\Model\MemberCreditLog;
use Module\Member\Type\MemberCreditFreezeStatus;

class MemberCreditUtil
{
    public static function checkOrFail($memberUserId = null, $creditValue = 1)
    {
        if (null === $memberUserId) {
            $memberUserId = MemberUser::id();
        }
        if (self::getTotal($memberUserId) < $creditValue) {
            BizException::throws(modstart_module_config('Member', 'creditName') . '不足');
        }
    }

    public static function paginateLog($memberUserId, $page, $pageSize, $option = [])
    {
        $option['where']['memberUserId'] = $memberUserId;
        $option['order'] = ['id', 'desc'];
        return ModelUtil::paginate(MemberCreditLog::class, $page, $pageSize, $option);
    }

    public static function get($memberUserId)
    {
        return ModelUtil::get(MemberCredit::class, [
            'memberUserId' => $memberUserId,
        ]);
    }

    public static function getTotal($memberUserId)
    {
        $m = ModelUtil::get(MemberCredit::class, [
            'memberUserId' => $memberUserId,
        ]);
        if (empty($m)) {
            return 0;
        }
        return $m['total'];
    }

    /**
     * 变更用户的积分
     * !!! 这个方法应该在事务中调用 !!!
     *
     * @param $memberUserId int 用户ID
     * @param $change int 变化值，正数为增加，负数为减少
     * @param $remark string 备注
     * @param $meta array|null 元数据
     * @param $option array 属性
     * @throws \Exception
     */
    public static function change($memberUserId, $change, $remark, $meta = null, $option = [])
    {
        $option = array_merge([
            // 检查是否为负
            'checkNegative' => true,
        ], $option);
        BizException::throwsIf('Member.Credit.change=0', !$change);
        $m = ModelUtil::getWithLock(MemberCredit::class, [
            'memberUserId' => $memberUserId
        ]);
        if (empty($m)) {
            $m = ModelUtil::insert(MemberCredit::class, [
                'memberUserId' => $memberUserId,
                'total' => 0,
                'freezeTotal' => 0,
            ]);
        }
        if ($option['checkNegative']) {
            BizException::throwsIf('Member.Credit.total<0', $change < 0 && $m['total'] + $change < 0);
        }
        if ($meta && !is_string($meta)) {
            $meta = SerializeUtil::jsonEncode($meta);
        }
        ModelUtil::insert(MemberCreditLog::class, [
            'memberUserId' => $memberUserId,
            'change' => $change,
            'remark' => $remark,
            'meta' => $meta,
        ]);
        ModelUtil::update(MemberCredit::class, $m['id'], [
            'total' => $m['total'] + $change,
        ]);
    }

    /**
     * 冻结用户积分（准备阶段）
     * !!! 这个方法应该在事务中调用 !!!
     *
     * @param $memberUserId int 用户ID
     * @param $value int 冻结值
     * @param $remark string 备注
     * @param $meta array|null 元数据
     * @return int 冻结ID
     * @throws BizException
     */
    public static function freezePrepare($memberUserId, $value, $remark, $meta = null)
    {
        BizException::throwsIf('MemberCreditUtil.freezePrepare.value>0', $value <= 0);
        $m = ModelUtil::getWithLock(MemberCredit::class, ['memberUserId' => $memberUserId]);
        if (empty($m)) {
            $m = ModelUtil::insert(MemberCredit::class, [
                'memberUserId' => $memberUserId,
                'total' => 0,
                'freezeTotal' => 0,
            ]);
        }
        BizException::throwsIf('MemberCreditUtil.freezePrepare.total<0', $m['total'] - $value < 0);
        if ($meta && !is_string($meta)) {
            $meta = SerializeUtil::jsonEncode($meta);
        }
        ModelUtil::update(MemberCredit::class, $m['id'], [
            'total' => $m['total'] - $value,
            'freezeTotal' => $m['freezeTotal'] + $value,
        ]);
        $freeze = ModelUtil::insert(MemberCreditFreeze::class, [
            'memberUserId' => $memberUserId,
            'freezeAt' => date('Y-m-d H:i:s'),
            'status' => MemberCreditFreezeStatus::PROCESSING,
            'value' => $value,
            'remark' => $remark,
            'meta' => $meta,
        ]);
        return $freeze['id'];
    }

    /**
     * 冻结用户积分（提交阶段）
     * !!! 这个方法应该在事务中调用 !!!
     * @param $memberUserId int 用户ID
     * @param $freezeId int 冻结ID
     * @throws BizException
     */
    public static function freezeCommit($memberUserId, $freezeId)
    {
        $freeze = ModelUtil::getWithLock(MemberCreditFreeze::class, [
            'id' => $freezeId,
        ]);
        BizException::throwsIfEmpty('MemberCreditUtil.freezeCommit.empty', $freeze);
        BizException::throwsIf('MemberCreditUtil.freezeCommit.memberError', $freeze['memberUserId'] != $memberUserId);
        BizException::throwsIf('MemberCreditUtil.freezeCommit.status', $freeze['status'] != MemberCreditFreezeStatus::PROCESSING);
        $m = ModelUtil::getWithLock(MemberCredit::class, ['memberUserId' => $memberUserId]);
        BizException::throwsIfEmpty('MemberCreditUtil.freezeCommit.empty', $m);
        BizException::throwsIf('MemberCreditUtil.freezeCommit.total<0', $m['freezeTotal'] - $freeze['value'] < 0);
        ModelUtil::update(MemberCreditFreeze::class, $freeze['id'], [
            'status' => MemberCreditFreezeStatus::COMMITTED,
            'commitAt' => date('Y-m-d H:i:s'),
        ]);
        ModelUtil::update(MemberCredit::class, $m['id'], [
            'freezeTotal' => $m['freezeTotal'] - $freeze['value'],
        ]);
        ModelUtil::insert(MemberCreditLog::class, [
            'memberUserId' => $memberUserId,
            'change' => -$freeze['value'],
            'remark' => $freeze['remark'],
            'meta' => SerializeUtil::jsonEncode([
                'freezeId' => $freeze['id'],
            ]),
        ]);
    }

    /**
     * 冻结用户积分（取消阶段）
     * !!! 这个方法应该在事务中调用 !!!
     * @param $memberUserId int 用户ID
     * @param $freezeId int 冻结ID
     * @throws BizException
     */
    public static function freezeCancel($memberUserId, $freezeId)
    {
        $freeze = ModelUtil::getWithLock(MemberCreditFreeze::class, [
            'id' => $freezeId,
        ]);
        BizException::throwsIfEmpty('MemberCreditUtil.freezeCancel.empty', $freeze);
        BizException::throwsIf('MemberCreditUtil.freezeCancel.memberError', $freeze['memberUserId'] != $memberUserId);
        BizException::throwsIf('MemberCreditUtil.freezeCancel.status', $freeze['status'] != MemberCreditFreezeStatus::PROCESSING);
        ModelUtil::update(MemberCreditFreeze::class, $freeze['id'], [
            'status' => MemberCreditFreezeStatus::CANCELED,
            'cancelAt' => date('Y-m-d H:i:s'),
        ]);
        $m = ModelUtil::getWithLock(MemberCredit::class, ['memberUserId' => $memberUserId]);
        BizException::throwsIfEmpty('MemberCreditUtil.freezeCancel.empty', $m);
        ModelUtil::update(MemberCredit::class, $m['id'], [
            'total' => $m['total'] + $freeze['value'],
            'freezeTotal' => $m['freezeTotal'] - $freeze['value'],
        ]);
    }

}
