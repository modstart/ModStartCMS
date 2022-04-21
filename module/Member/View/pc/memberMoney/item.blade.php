<div class="tw-bg-gray-200 tw-rounded-sm tw-mb-2 tw-box tw-px-5 tw-py-3 tw-mb-3 tw-flex tw-items-center tw-zoom-in" data-repeat="3">
    <div class="tw-mr-auto"><div class="tw-font-medium">{{$item->remark}}</div>
        <div class="tw-text-gray-600 tw-text-xs tw-mt-0.5">{{$item->created_at}}</div>
    </div>
    @if($item->change>0)
        <div class="tw-text-green-600"><span class="tw-text-gray-400 tw-mr-3">收入</span> +￥{{$item->change}}</div>
    @else
        <div class="tw-text-red-600"><span class="tw-text-gray-400 tw-mr-3">支出</span> -￥{{sprintf('%.2f',-$item->change)}}</div>
    @endif
</div>
