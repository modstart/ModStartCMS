<div class="ub-content-box hover:tw-shadow-lg tw-h-36 margin-bottom">
    <div class="ub-border-bottom tw-leading-6">
        @if(!empty($icon))
            <i class="tw-mr-1 {{$icon}}"></i>
        @endif
        {{$title}}
    </div>
    <div class="tw-flex tw-text-center tw-py-7">
        @foreach($value as $v)
            <div class="tw-flex-grow">
                <div class="tw-text-xl tw-pb-2">
                    @if(!empty($v['url']))
                        <a class="tw-text-black hover:tw-text-blue-400"
                           data-tab-open data-tab-title="加载中..."
                           href="{{$v['url']}}">{{$v['value']}}</a>
                    @else
                        <span class="tw-text-black">{{$v['value']}}</span>
                    @endif
                </div>
                <div class="ub-text-tertiary">{{$v['title']}}</div>
            </div>
        @endforeach
    </div>
</div>
