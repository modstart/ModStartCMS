<?php


namespace Module\Member\Api\Controller;


use Illuminate\Routing\Controller;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\SerializeUtil;
use Module\Member\Auth\MemberUser;
use Module\Member\Auth\MemberVip;
use Module\Member\Core\MemberVipPayCenterBiz;
use Module\Member\Core\MemberVipVoucherBiz;
use Module\Member\Model\MemberVipOrder;
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
        $data['title'] = modstart_config('Member_VipTitle', '会员协议');
        $data['content'] = modstart_config('Member_VipContent');
        $data['countDown'] = modstart_config('Member_VipCountDown', 1800);
        $data['vips'] = MemberVipUtil::allVisible();
        $data['rights'] = MemberVipUtil::rights();
        $data['openUsers'] = MemberVipUtil::openUsers();
        return Response::generateSuccessData($data);
    }

    public function all()
    {
        return Response::generateSuccessData(MemberVipUtil::allVisible());
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
        $priceInfoRet = $this->processCalc();
        if ($priceInfoRet['code']) {
            return Response::generateError($priceInfoRet['msg']);
        }
        $money = $priceInfoRet['data']['price'];
        if ($money < 0.01) {
            return Response::generateError('支付金额为空0.01');
        }
        try {
            ModelUtil::transactionBegin();
            $orderParam = [];
            if (!empty($priceInfoRet['data']['usedVoucherItems'])) {
                $orderParam['voucherItemIds'] = array_column($priceInfoRet['data']['usedVoucherItems'], 'id');
            }
            $memberVipOrder = ModelUtil::insert(MemberVipOrder::class, [
                'status' => OrderStatus::WAIT_PAY,
                'memberUserId' => MemberUser::id(),
                'vipId' => $memberVip['id'],
                'payFee' => $money,
                'expire' => $priceInfoRet['data']['expire'],
                'type' => $priceInfoRet['data']['type'],
                'param' => SerializeUtil::jsonEncode($orderParam),
            ]);
            if (!empty($priceInfoRet['data']['usedVoucherItems'])) {
                MemberVipVoucherBiz::bizer()->processUpdateUsedItemsInTransactionOrFail(
                    MemberUser::id(),
                    $priceInfoRet['data']['usedVoucherItems'],
                    'MemberVipOrder',
                    $memberVipOrder['id']
                );
            }
            ModelUtil::transactionCommit();
        } catch (BizException $e) {
            ModelUtil::transactionRollback();
            return Response::generateError($e->getMessage());
        } catch (\Exception $e) {
            ModelUtil::transactionRollback();
            throw $e;
        }
        $payCenterPerform = app(PayCenterPerform::class);
        return $payCenterPerform->performSubmitOrder(
            MemberVipPayCenterBiz::NAME,
            $memberVipOrder['id'],
            $memberVipOrder['payFee'],
            '购买会员'
        );
    }

    public function calc()
    {
        $ret = $this->processCalc();
        unset($ret['data']['usedVoucherItems']);
        return $ret;
    }

    private static function processCalc()
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
        $data = $ret['data'];
        if (modstart_module_enabled('Voucher')) {
            $voucherId = $input->getInteger('voucherId');
            if ($voucherId > 0) {
                $bizer = MemberVipVoucherBiz::bizer();
                $voucherItems = MemberVipVoucherBiz::listValidForMemberWithItemIds(MemberUser::id(), [$voucherId]);
                $voucherItems = $bizer->processFindUsableItems(MemberUser::id(), $voucherItems);
                $processResult = $bizer->processComputeItems(MemberUser::id(), $voucherItems, [
                    'price' => $data['price'],
                ]);
                $data['price'] = $processResult['price'];
                $data['usedVoucherItems'] = $processResult['usedVoucherItems'];
            }
        }
        return Response::generateSuccessData($data);
    }
}
