<?php

namespace Module\Member\Util;

use ModStart\Core\Dao\ModelUtil;

class MemberCreditUtil
{
    public static function paginateLog($memberUserId, $page, $pageSize, $option = [])
    {
        $option['where']['memberUserId'] = $memberUserId;
        $option['order'] = ['id', 'desc'];
        return ModelUtil::paginate('member_credit_log', $page, $pageSize, $option);
    }

    public static function getTotal($memberUserId)
    {
        $m = ModelUtil::get('member_credit', ['memberUserId' => $memberUserId]);
        if (empty($m)) {
            return 0;
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
            throw new \Exception('MemberCreditUtil -> change empty');
        }
        $m = ModelUtil::getWithLock('member_credit', ['memberUserId' => $memberUserId]);
        if (empty($m)) {
            $m = ModelUtil::insert('member_credit', ['memberUserId' => $memberUserId, 'total' => 0,]);
        }
        if ($change < 0 && $m['total'] + $change < 0) {
            throw new \Exception('MemberCreditUtil -> total change to empty');
        }
        ModelUtil::insert('member_credit_log', ['memberUserId' => $memberUserId, 'change' => $change, 'remark' => $remark]);
        $m = ModelUtil::update('member_credit', ['id' => $m['id']], ['total' => $m['total'] + $change]);
        if ($m['total'] < 0) {
            throw new \Exception('MemberCreditUtil -> total empty');
        }
    }

}
