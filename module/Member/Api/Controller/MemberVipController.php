<?php


namespace Module\Member\Api\Controller;


use Illuminate\Routing\Controller;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use Module\Member\Auth\MemberUser;
use Module\Member\Auth\MemberVip;
use Module\Member\Constant\PayConstant;
use Module\Member\Support\MemberLoginCheck;
use Module\Member\Util\MemberVipUtil;
use Module\PayCenter\Support\PayCenterPerform;
use Module\Vendor\Type\OrderStatus;

class MemberVipController extends Controller implements MemberLoginCheck
{
    public function all()
    {
        return Response::generateSuccessData(MemberVipUtil::all());
    }

    public function buy(PayCenterPerform $payCenterPerform)
    {
        $input = InputPackage::buildFromInput();
        $vipId = $input->getInteger('vipId');
        if (empty($vipId)) {
            return Response::generateError('请选择会员类型');
        }
        $memberVip = MemberVipUtil::get($vipId);
        if (empty($memberVip)) {
            return Response::generateError('请选择会员类型');
        }
        $priceInfoRet = $this->calc($vipId);
        if ($priceInfoRet['code']) {
            return Response::generateError($priceInfoRet['msg']);
        }
        $money = $priceInfoRet['data']['price'];
        if ($money < 0.01) {
            return Response::generateError('支付金额为空0.01');
        }
        $memberVipOrder = ModelUtil::insert('member_vip_order', [
            'status' => OrderStatus::WAIT_PAY,
            'memberUserId' => MemberUser::id(),
            'vipId' => $memberVip['id'],
            'payFee' => $money,
            'expire' => $priceInfoRet['data']['expire'],
            'type' => $priceInfoRet['data']['type'],
        ]);
        return $payCenterPerform->performSubmitOrder(
            PayConstant::MEMBER_VIP,
            $memberVipOrder['id'],
            $memberVipOrder['payFee'],
            '购买会员'
        );
    }

    public function calc($vipId = 0)
    {
        $input = InputPackage::buildFromInput();
        $memberVip = MemberVip::get();
        if (empty($vipId)) {
            $vipId = $input->getInteger('vipId');
        }
        if (empty($vipId)) {
            return Response::generateError('请选择会员');
        }
        $ret = MemberVipUtil::calcPrice($memberVip ? $memberVip['id'] : 0, MemberUser::get('vipExpire'), $vipId);
        if ($ret['code']) {
            return Response::generateError($ret['msg']);
        }
        return Response::generateSuccessData($ret['data']);
    }
}
