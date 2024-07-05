<?php


namespace Module\Member\Core;


use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\SerializeUtil;
use ModStart\Module\ModuleManager;
use Module\Member\Events\MemberUserVipChangeEvent;
use Module\Member\Model\MemberVipOrder;
use Module\Member\Util\MemberCreditUtil;
use Module\Member\Util\MemberUtil;
use Module\Member\Util\MemberVipUtil;
use Module\PayCenter\Biz\AbstractPayCenterBiz;
use Module\Vendor\Type\OrderStatus;

class MemberVipPayCenterBiz extends AbstractPayCenterBiz
{
    const NAME = 'mMemberVip';

    public function name()
    {
        return self::NAME;
    }

    public function title()
    {
        return '会员VIP';
    }

    public function onPayed($payBizId, $payOrder, $param = [])
    {
        $order = ModelUtil::get('member_vip_order', $payBizId);
        if (empty($order)) {
            return;
        }
        ModelUtil::update('member_vip_order', ['id' => $payBizId], ['status' => OrderStatus::COMPLETED]);
        $memberUser = MemberUtil::get($order['memberUserId']);
        if (empty($memberUser)) {
            return;
        }
        $update = [];
        $update['vipId'] = $order['vipId'];
        $update['vipExpire'] = $order['expire'];
        MemberUtil::update($order['memberUserId'], $update);
        if (ModuleManager::getModuleConfig('Member', 'creditEnable', false)) {
            $vipSet = MemberVipUtil::get($order['vipId']);
            if ($vipSet['creditPresentEnable']) {
                if ($vipSet['creditPresentValue'] > 0) {
                    MemberCreditUtil::change($order['memberUserId'], $vipSet['creditPresentValue'], '会员VIP赠送积分');
                }
            }
        }
        if ($update['vipId'] != $memberUser['vipId']) {
            MemberUserVipChangeEvent::fire($order['memberUserId'], $memberUser['vipId'], $update['vipId']);
        }
    }

    public function createOrderForQuick($quickOrder, $param = [])
    {
        $memberUserId = $quickOrder['session']['memberUserId'];
        BizException::throwsIfEmpty('用户ID为空', $memberUserId);
        $vipId = $quickOrder['param']['vipId'];
        BizException::throwsIfEmpty('请选择会员类型', $vipId);
        $memberVip = MemberVipUtil::get($vipId);
        BizException::throwsIfEmpty('会员类型不存在', $memberVip);
        $memberUser = MemberUtil::get($memberUserId);
        BizException::throwsIfEmpty('会员不存在', $memberUser);
        // $api = app(MemberVipController::class);
        // $priceInfoRet = $api->calc($vipId);
        $priceInfoRet = MemberVipUtil::calcPrice($memberUser['vipId'], $memberUser['vipExpire'], $vipId);
        BizException::throwsIf($priceInfoRet['msg'], $priceInfoRet['code'] > 0);
        $money = $priceInfoRet['data']['price'];
        if (modstart_module_enabled('Voucher')) {
            $voucherId = isset($quickOrder['param']['voucherId']) ? intval($quickOrder['param']['voucherId']) : 0;
            if ($voucherId > 0) {
                $bizer = MemberVipVoucherBiz::bizer();
                $voucherItems = MemberVipVoucherBiz::listValidForMemberWithItemIds($memberUserId, [$voucherId]);
                $voucherItems = $bizer->processFindUsableItems($memberUserId, $voucherItems);
                $processResult = $bizer->processComputeItems($memberUserId, $voucherItems, [
                    'price' => $money,
                ]);
                $money = $processResult['price'];
            }
        }
        // Log::info('MemberVipPayCenterBiz.createOrderForQuick - ' . json_encode([$quickOrder, $priceInfoRet], JSON_UNESCAPED_UNICODE));
        BizException::throwsIf('订单金额异常', $money < 0.01 || $money > 1000 * 10000);
        $orderParam = [];
        if (!empty($processResult['usedVoucherItems'])) {
            $orderParam['voucherItemIds'] = array_column($processResult['usedVoucherItems'], 'id');
        }
        $order = ModelUtil::insert(MemberVipOrder::class, [
            'status' => OrderStatus::WAIT_PAY,
            'memberUserId' => $memberUserId,
            'vipId' => $memberVip['id'],
            'payFee' => $money,
            'expire' => $priceInfoRet['data']['expire'],
            'type' => $priceInfoRet['data']['type'],
            'param' => SerializeUtil::jsonEncode($orderParam),
        ]);
        if (!empty($processResult['usedVoucherItems'])) {
            MemberVipVoucherBiz::bizer()->processUpdateUsedItemsInTransactionOrFail(
                $memberUserId,
                $processResult['usedVoucherItems'],
                'MemberVipOrder',
                $order['id']
            );
        }
        return Response::generateSuccessData([
            'bizId' => $order['id'],
            'feeTotal' => $money,
            'body' => '开通VIP',
            'param' => [],
            'redirect' => modstart_web_url('member_vip'),
        ]);
    }


}
