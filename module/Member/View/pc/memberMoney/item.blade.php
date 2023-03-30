<div class="tw-bg-gray-100 tw-rounded tw-mb-2 tw-box tw-p-3 tw-mb-3 tw-flex tw-items-center tw-zoom-in">
    <div class="tw-w-14 tw-flex-shrink-0">
        <div class="tw-h-10 tw-leading-10 tw-rounded-full tw-text-center tw-w-10 ub-bg-a ub-text-white tw-text-lg">
            <i class="iconfont icon-cny"></i>
        </div>
    </div>
    <div class="tw-mr-auto">
        <div class="tw-font-medium">{{$item->remark}}</div>
        <div class="tw-text-gray-600 tw-text-xs tw-mt-1 ub-text-tertiary">
            <i class="iconfont icon-time"></i>
            {{$item->created_at}}
        </div>
    </div>
    @if($item->change>0)
        <div class="tw-text-green-600">
            <span class="tw-text-gray-400 tw-mr-3">收入</span>
            <span class="tw-text-lg">+￥{{$item->change}}</span>
        </div>
    @else
        <div class="tw-text-red-600">
            <span class="tw-text-gray-400 tw-mr-3">支出</span>
            <span class="tw-text-lg">-￥{{sprintf('%.2f',-$item->change)}}</span>
        </div>
    @endif
</div>
