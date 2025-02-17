<div class="tw-p-2 tw-relative tw-items-center hover:tw-bg-gray-50 tw-rounded"
    data-message-id="{{$item->id}}">
    <div class="tw-flex">
        <div class="tw-w-20 tw-relative tw-flex-shrink-0">
            <div>
                @if(!$item->fromId)
                    <span class="tw-text-default tw-bg-gray-100 tw-inline-block tw-rounded tw-px-2">系统消息</span>
                @endif
            </div>
            @if($item->status==\Module\Member\Type\MemberMessageStatus::UNREAD)
                <div class="tw-absolute tw--left-1 tw-top-0" data-message-unread>
                    <i class="iconfont icon-dot-sm ub-text-danger"></i>
                </div>
            @endif
        </div>
        <div class="tw-w-36 ub-text-muted">
            {{ $item->created_at }}
        </div>
        <div class="tw-flex-grow tw-text-right">
            @if($item->status==\Module\Member\Type\MemberMessageStatus::UNREAD)
                <a href="javascript:;" data-item-read>已读</a>
            @endif
            <a href="javascript:;" class="ub-text-danger" data-item-delete>删除</a>
        </div>
    </div>
    <div class="tw-pt-2">
        <div class="ub-html">
            {!! $item->content !!}
        </div>
    </div>
</div>
<div class="tw-bg-gray-200 tw-my-1" style="height:1px;"></div>
