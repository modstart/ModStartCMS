<?php


namespace Module\Member\Api\Controller;


use Illuminate\Routing\Controller;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use ModStart\Core\Type\TypeUtil;
use Module\Member\Auth\MemberUser;
use Module\Member\Support\MemberLoginCheck;
use Module\Member\Type\MemberMoneyCashType;
use Module\Member\Util\MemberMoneyUtil;

/**
 * Class MemberMoneyCashController
 * @package Module\Member\Api\Controller
 * @Api 会员资金
 */
class MemberMoneyCashController extends Controller implements MemberLoginCheck
{
    /**
     * @return array
     * @Api 获取提现配置
     */
    public function get()
    {
        $total = MemberMoneyUtil::getTotal(MemberUser::id());
        $min = modstart_config('Member_MoneyCashMin', 100);
        return Response::generateSuccessData([
            'total' => $total,
            'desc' => modstart_config('Member_MoneyCashDescription'),
            'min' => sprintf('%0.2f', $min),
            'rate' => modstart_config('Member_MoneyCashTaxRate', 0),
            'types' => TypeUtil::dump(MemberMoneyCashType::class),
            'canCash' => $total >= $min,
            'defaultType' => MemberMoneyCashType::ALIPAY,
        ]);
    }

    /**
     * @return array
     * @Api 计算提现金额
     * @ApiBodyParam money float 提现金额
     */
    public function calc()
    {
        $input = InputPackage::buildFromInput();
        $money = $input->getDecimal('money');
        if ($money < modstart_config('Member_MoneyCashMin', 100)) {
            return Response::generateError('最小提现金额为' . modstart_config('Member_MoneyCashMin', 100));
        }
        $total = MemberMoneyUtil::getTotal(MemberUser::id());
        if ($money > $total) {
            return Response::generateError('余额不足');
        }
        $rate = modstart_config('Member_MoneyCashTaxRate', 0);
        $rate = 100 - min(max($rate, 0), 99);
        $value = bcdiv(bcmul($money, $rate, 2), 100, 2);
        return Response::generateSuccessData([
            'value' => $value,
        ]);
    }

    /**
     * @return array
     * @throws \Exception
     * @Api 提现提交
     * @ApiBodyParam money float 提现金额
     * @ApiBodyParam type string 提现方式 1支付宝
     * @ApiBodyParam alipayRealname string 支付宝真实姓名
     * @ApiBodyParam alipayAccount string 支付宝账号
     */
    public function submit()
    {
        if (!modstart_config('Member_MoneyCashEnable', false)) {
            return Response::generateError('功能未开启');
        }
        $input = InputPackage::buildFromInput();
        $money = $input->getDecimal('money');
        if ($money < 0.01) {
            return Response::generate(-1, '提现金额不能为空');
        }
        if ($money < modstart_config('Member_MoneyCashMin', 100)) {
            return Response::generate(-1, '提现金额至少为' . modstart_config('Member_MoneyCashMin', 100));
        }
        $type = $input->getType('type', MemberMoneyCashType::class);
        switch ($type) {
            case MemberMoneyCashType::ALIPAY:
                $alipayRealname = $input->getTrimString('alipayRealname');
                $alipayAccount = $input->getTrimString('alipayAccount');
                if (empty($alipayRealname)) {
                    return Response::generate(-1, '支付宝姓名不能为空');
                }
                if (empty($alipayAccount)) {
                    return Response::generate(-1, '支付宝账号不能为空');
                }
                break;
            default:
                return Response::generateError('支付类型错误');
        }
        $total = MemberMoneyUtil::getTotal(MemberUser::id());
        if ($total < modstart_config('Member_MoneyCashMin', 100)) {
            return Response::generate(-1, '当前账户余额不满' . modstart_config('Member_MoneyCashMin', 100) . ',不能提现');
        }
        $rate = modstart_config('Member_MoneyCashTaxRate', 0);
        $rate = 100 - min(max($rate, 0), 99);
        $moneyAfterTax = bcdiv(bcmul($money, $rate, 2), 100, 2);
        try {
            ModelUtil::transactionBegin();
            MemberMoneyUtil::cash(MemberUser::id(), $money, $moneyAfterTax, MemberMoneyCashType::ALIPAY, $alipayRealname, $alipayAccount);
            ModelUtil::transactionCommit();
        } catch (\Exception $e) {
            ModelUtil::transactionRollback();
            throw $e;
        }
        return Response::generate(0, '提交成功', null, modstart_web_url('member_money/cash/log'));
    }

    public function log()
    {
        $input = InputPackage::buildFromInput();
        $option = [];
        $paginateData = MemberMoneyUtil::paginateCash(
            MemberUser::id(),
            $input->getPage(),
            $input->getPageSize(),
            $option
        );
        return Response::generateSuccessPaginate(
            $input->getPage(),
            $input->getPageSize(),
            $paginateData
        );
    }
}
