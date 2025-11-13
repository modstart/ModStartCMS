<div class="tw-bg-white ub-border tw-p-4 tw-rounded-lg margin-bottom">
    <div class="tw-flex">
        <div class="tw-flex-grow">
            {{$item->created_at}}
        </div>
        <div>
            @if($item->status==\Module\Member\Type\MemberMoneyCashStatus::SUCCESS)
                <span
                    class="ub-text-success">{{\ModStart\Core\Type\TypeUtil::name(\Module\Member\Type\MemberMoneyCashStatus::class,\Module\Member\Type\MemberMoneyCashStatus::SUCCESS)}}</span>
            @endif
            @if($item->status==\Module\Member\Type\MemberMoneyCashStatus::VERIFYING)
                <span
                    class="ub-text-warning">{{\ModStart\Core\Type\TypeUtil::name(\Module\Member\Type\MemberMoneyCashStatus::class,\Module\Member\Type\MemberMoneyCashStatus::VERIFYING)}}</span>
            @endif
        </div>
    </div>
    <div class="tw-flex">
        <div class="tw-flex-grow">
            <div class="tw-text-gray-600 tw-text-sm tw-mt-1">
                {{\ModStart\Core\Type\TypeUtil::name(\Module\Member\Type\MemberMoneyCashType::class,$item->type)}}
            </div>
            <div class="tw-text-gray-600 tw-text-sm tw-mt-1">
                姓名：{{$item->realname}}
            </div>
            <div class="tw-text-gray-600 tw-text-sm tw-mt-1">
                账号：{{$item->account}}
            </div>
            @if($item->type===\Module\Member\Type\MemberMoneyCashType::BANK)
                <div class="tw-text-gray-600 tw-text-sm tw-mt-1">
                    开户行：{{$item->param['bankName']}}
                </div>
            @endif
            <div class="tw-text-gray-600 tw-text-sm tw-mt-1">
                备注：{{$item->remark}}
            </div>
        </div>
        <div class="ub-text-success tw-font-bold">
            ￥{{$item->moneyAfterTax}}
        </div>
    </div>
</div>

