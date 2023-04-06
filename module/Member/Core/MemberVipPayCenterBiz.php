<?php


namespace Module\Member\Core;


use ModStart\Core\Dao\ModelUtil;
use ModStart\Module\ModuleManager;
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
        $update = [];
        $update['vipId'] = $order['vipId'];
        $update['vipExpire'] = $order['expire'];
        MemberUtil::update($order['memberUserId'], $update);
        if (ModuleManager::getModuleConfig('Member', 'creditEnable', false)) {
            $vipSet = MemberVipUtil::get($order['vipId']);
            if ($vipSet['creditPresentEnable']) {
                MemberCreditUtil::change($order['memberUserId'], $vipSet['creditPresentValue'], '会员VIP赠送积分');
            }
        }
    }


}
