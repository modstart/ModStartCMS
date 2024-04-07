<?php


namespace Module\Vendor\Util;


use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Exception\BizException;
use Module\Vendor\Type\OrderStatus;

class OrderUtil
{
    /**
     * 订单手动取消快捷方法
     * @param $model string 模型类名
     * @param $where array 查询条件
     * @param $successCallback callable 成功回调
     * @return void
     * @throws BizException
     */
    public static function cancelManual($model, $where, $successCallback = null)
    {
        ModelUtil::transactionBegin();
        try {
            $order = ModelUtil::getWithLock($model, $where);
            BizException::throwsIfEmpty('订单不存在', $order);
            BizException::throwsIf('订单状态异常', $order['status'] != OrderStatus::WAIT_PAY);
            $update = [
                'status' => OrderStatus::CANCEL,
            ];
            if (array_key_exists('cancelTime', $order)) {
                $update['cancelTime'] = date('Y-m-d H:i:s');
            }
            ModelUtil::update($model, $order['id'], $update);
            call_user_func_array($successCallback, [$order]);
            ModelUtil::transactionCommit();
        } catch (BizException $e) {
            ModelUtil::transactionRollback();
            throw $e;
        }
    }

    public static function cancelExpire($model, $where, $successCallback)
    {
        ModelUtil::transactionBegin();
        try {
            $order = ModelUtil::getWithLock($model, $where);
            BizException::throwsIfEmpty('订单不存在', $order);
            BizException::throwsIf('订单状态异常', $order['status'] != OrderStatus::WAIT_PAY);
            $update = [
                'status' => OrderStatus::CANCEL_EXPIRED,
            ];
            if (array_key_exists('cancelTime', $order)) {
                $update['cancelTime'] = date('Y-m-d H:i:s');
            }
            ModelUtil::update($model, $order['id'], $update);
            call_user_func_array($successCallback, [$order]);
            ModelUtil::transactionCommit();
        } catch (BizException $e) {
            ModelUtil::transactionRollback();
        }
    }

    public static function payed($model, $where, $successCallback, $updateStatus = null)
    {
        if (is_null($updateStatus)) {
            $updateStatus = OrderStatus::COMPLETED;
        }
        ModelUtil::transactionBegin();
        try {
            $order = ModelUtil::getWithLock($model, $where);
            BizException::throwsIfEmpty('订单不存在', $order);
            BizException::throwsIf('订单状态异常', $order['status'] != OrderStatus::WAIT_PAY);
            $update = [
                'status' => $updateStatus,
            ];
            if (array_key_exists('payTime', $order)) {
                $update['payTime'] = date('Y-m-d H:i:s');
            }
            ModelUtil::update($model, $order['id'], $update);
            call_user_func_array($successCallback, [$order]);
            ModelUtil::transactionCommit();
        } catch (BizException $e) {
            ModelUtil::transactionRollback();
            throw $e;
        }
    }
}
