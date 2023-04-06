<?php


namespace Module\Member\Api\Controller;


use Illuminate\Routing\Controller;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use Module\Member\Auth\MemberUser;
use Module\Member\Auth\MemberVip;
use Module\Member\Core\MemberVipPayCenterBiz;
use Module\Member\Support\MemberLoginCheck;
use Module\Member\Util\MemberVipUtil;
use Module\PayCenter\Support\PayCenterPerform;
use Module\Vendor\Type\OrderStatus;

class MemberVipController extends Controller implements MemberLoginCheck
{
    public static $memberLoginCheckIgnores = [
        'info', 'calc',
    ];

    public function info()
    {
        $data = [];
        $data['title'] = modstart_config('Member_VipTitle');
        $data['subTitle'] = modstart_config('Member_VipSubTitle');
        $data['content'] = modstart_config('Member_VipContent');
        $data['vips'] = MemberVipUtil::all();
        $data['rights'] = MemberVipUtil::rights();
        return Response::generateSuccessData($data);
    }

    public function all()
    {
        return Response::generateSuccessData(MemberVipUtil::all());
    }

    public function buy()
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
        BizException::throwsIf('请安装 PayCenter 模块', !modstart_module_enabled('PayCenter'));
        $payCenterPerform = app(PayCenterPerform::class);
        return $payCenterPerform->performSubmitOrder(
            MemberVipPayCenterBiz::NAME,
            $memberVipOrder['id'],
            $memberVipOrder['payFee'],
            '购买会员'
        );
    }

    public function calc($vipId = 0)
    {
        $input = InputPackage::buildFromInput();
        if (MemberUser::isNotLogin()) {
            return Response::generate(0, 'ok', [
                'type' => '-',
                'expire' => '-',
                'price' => '-',
            ]);
        }
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
