<?php

namespace Module\Member\Core;

use ModStart\Core\Util\NumberUtil;
use ModStart\Form\Form;
use Module\Voucher\Biz\AbstractVoucherBiz;
use Module\Voucher\Type\VoucherType;

class MemberVipVoucherBiz extends AbstractVoucherBiz
{
    const NAME = 'MemberVip';

    public function name()
    {
        return self::NAME;
    }

    public function title()
    {
        return '会员VIP';
    }

    public function adminRenderForm(Form $form)
    {
        $form->radio('type', '类型')
            ->options(VoucherType::only([VoucherType::DISCOUNT]))
            ->when('=', VoucherType::DISCOUNT, function (Form $form) {
                $form->number('typeDiscount', '折扣')->help('0-100，80表示打八折');
            })
            ->required()
            ->defaultValue(VoucherType::DISCOUNT);
    }

    public function itemUsable($voucherItemData, $memberUserId, $data = [])
    {
        $vipId = $data['vipId'];
        if (empty($vipId)) {
            return false;
        }
        switch ($voucherItemData['_voucher']['type']) {
            case VoucherType::DISCOUNT:
                return true;
        }
        return false;
    }

    public function processComputeItems($memberUserId, $usingVoucherItemDataList, $data = [], $param = [])
    {
        $data['usedVoucherItems'] = $usingVoucherItemDataList;
        // 折扣券
        foreach ($this->filterVoucherItems($usingVoucherItemDataList, VoucherType::DISCOUNT) as $item) {
            $discount = $item['_voucher']['typeDiscount'];
            $data['price'] = NumberUtil::discountDecimal($data['price'], $discount);
        }
        return $data;
    }

}
