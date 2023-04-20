<?php


namespace Module\Member\Api\Controller;


use Illuminate\Routing\Controller;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\InputPackage;
use Module\Member\Auth\MemberUser;
use Module\Member\Core\MemberMoneyChargePayCenterBiz;
use Module\Member\Support\MemberLoginCheck;
use Module\PayCenter\Support\PayCenterPerform;
use Module\Vendor\Type\OrderStatus;

class MemberMoneyChargeController extends Controller implements MemberLoginCheck
{
    public function submit()
    {
        BizException::throwsIf('钱包充值未开启',!modstart_config('Member_MoneyChargeEnable',false));
        $input = InputPackage::buildFromInput();
        $money = $input->getDecimal('money');
        BizException::throwsIf('金额最少为0.01元', $money < 0.01);
        BizException::throwsIf('金额最大为1,000,000元', $money > 1000000);
        $order = ModelUtil::insert('member_money_charge_order', [
            'status' => OrderStatus::WAIT_PAY,
            'memberUserId' => MemberUser::id(),
            'money' => $money,
        ]);
        BizException::throwsIf('请安装 PayCenter 模块', !modstart_module_enabled('PayCenter'));
        $payCenterPerform = app(PayCenterPerform::class);
        return $payCenterPerform->performSubmitOrder(
            MemberMoneyChargePayCenterBiz::NAME,
            $order['id'],
            $order['money'],
            '钱包充值'
        );
    }
}
