<?php


namespace Module\Member\Api\Controller;


use Illuminate\Routing\Controller;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Exception\BizException;
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
 * @Api 用户信息
 */
class MemberMoneyCashController extends Controller implements MemberLoginCheck
{
    /**
     * @Api 获取提现配置
     * @ApiMethod post
     * @ApiHeadParam api-token string required 登录凭证
     * @ApiDesc 获取当前用户的余额提现配置信息
     * @ApiResponseData {
     *   "code": 0,
     *   "data": {
     *     "total": "100.00",
     *     "desc": "提现说明",
     *     "min": "100.00",
     *     "rate": 0,
     *     "types": {
     *       "1": "支付宝"
     *     },
     *     "canCash": true,
     *     "defaultType": 1
     *   }
     * }
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
     * @Api 计算提现金额
     * @ApiMethod post
     * @ApiHeadParam api-token string required 登录凭证
     * @ApiDesc 计算提现扣除手续费后的实际到账金额
     * @ApiBodyParam money float required 提现金额
     * @ApiResponseData {
     *   "code": 0,
     *   "data": {
     *     "value": "100.00"
     *   }
     * }
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
     * @Api 提现提交
     * @ApiMethod post
     * @ApiHeadParam api-token string required 登录凭证
     * @ApiDesc 提交余额提现申请
     * @ApiBodyParam money float required 提现金额
     * @ApiBodyParam type string required 提现方式 1支付宝 2微信 3银行
     * @ApiBodyParam alipayRealname string 支付宝真实姓名（支付宝提现必填）
     * @ApiBodyParam alipayAccount string 支付宝账号（支付宝提现必填）
     * @ApiBodyParam wechatRealname string 微信真实姓名（微信提现必填）
     * @ApiBodyParam wechatAccount string 微信账号（微信提现必填）
     * @ApiBodyParam bankRealname string 银行真实姓名（银行提现必填）
     * @ApiBodyParam bankAccount string 银行账号（银行提现必填）
     * @ApiBodyParam bankName string 银行名称（银行提现必填）
     * @ApiResponseData {
     *   "code": 0
     * }
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
        $remark = '余额提现';
        $realname = null;
        $account = null;
        $param = [];
        switch ($type) {
            case MemberMoneyCashType::ALIPAY:
                $realname = $input->getTrimString('alipayRealname');
                $account = $input->getTrimString('alipayAccount');
                BizException::throwsIfEmpty($realname, '支付宝姓名不能为空');
                BizException::throwsIfEmpty($account, '支付宝账号不能为空');
                break;
            case MemberMoneyCashType::WECHAT:
                $realname = $input->getTrimString('wechatRealname');
                $account = $input->getTrimString('wechatAccount');
                BizException::throwsIfEmpty($realname, '微信姓名不能为空');
                BizException::throwsIfEmpty($account, '微信账号不能为空');
                break;
            case MemberMoneyCashType::BANK:
                $realname = $input->getTrimString('bankRealname');
                $account = $input->getTrimString('bankAccount');
                $bankName = $input->getTrimString('bankName');
                BizException::throwsIfEmpty($realname, '银行姓名不能为空');
                BizException::throwsIfEmpty($account, '银行账号不能为空');
                BizException::throwsIfEmpty($bankName, '银行名称不能为空');
                $param['bankName'] = $bankName;
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
            MemberMoneyUtil::cash(MemberUser::id(), $money, $moneyAfterTax, $type, $realname, $account, $remark, $param);
            ModelUtil::transactionCommit();
        } catch (\Exception $e) {
            ModelUtil::transactionRollback();
            throw $e;
        }
        return Response::generate(0, '提交成功', null, modstart_web_url('member_money/cash/log'));
    }

    /**
     * @Api 提现记录
     * @ApiMethod post
     * @ApiHeadParam api-token string required 登录凭证
     * @ApiDesc 分页获取当前用户的提现记录
     * @ApiBodyParam page int 页码
     * @ApiBodyParam pageSize int 每页数量
     * @ApiResponseData {
     *   "code": 0,
     *   "data": {
     *     "page": 1,
     *     "pageSize": 10,
     *     "records": [
     *       {
     *         "id": 1,
     *         "money": "100.00"
     *       }
     *     ],
     *     "total": 1,
     *     "maxRecords": -1
     *   }
     * }
     */
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
