<div class="tw-bg-gray-100 tw-rounded tw-mb-2 tw-box tw-px-5 tw-py-3 tw-mb-3 tw-flex tw-items-center tw-zoom-in" data-repeat="3">
    <div class="tw-mr-auto">
        <div class="tw-font-medium">
            {{\ModStart\Core\Type\TypeUtil::name(\Module\Member\Type\MemberMoneyCashType::class,$item->type)}}
            @if($item->status==\Module\Member\Type\MemberMoneyCashStatus::SUCCESS)
                <span class="ub-text-success">{{\ModStart\Core\Type\TypeUtil::name(\Module\Member\Type\MemberMoneyCashStatus::class,\Module\Member\Type\MemberMoneyCashStatus::SUCCESS)}}</span>
            @endif
            @if($item->status==\Module\Member\Type\MemberMoneyCashStatus::VERIFYING)
                <span class="ub-text-warning">{{\ModStart\Core\Type\TypeUtil::name(\Module\Member\Type\MemberMoneyCashStatus::class,\Module\Member\Type\MemberMoneyCashStatus::VERIFYING)}}</span>
            @endif
        </div>
        <div class="tw-text-gray-600 tw-text-xs tw-mt-1">
            姓名：{{$item->realname}}
        </div>
        <div class="tw-text-gray-600 tw-text-xs tw-mt-1">
            账号：{{$item->account}}
        </div>
{{--        <div class="tw-text-gray-600 tw-text-xs tw-mt-1">--}}
{{--            备注：{{$item->remark}}--}}
{{--        </div>--}}
    </div>
    <div class="tw-text-green-600 tw-text-lg">
        <span class="tw-text-gray-400 tw-mr-3">提现</span>
        ￥{{$item->moneyAfterTax}}
    </div>
</div>

