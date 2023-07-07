<div class="ub-panel">
    <div class="head">
        <div class="title">
            <i class="iconfont icon-users"></i>
            {!! modstart_config('Partner_Title','合作伙伴') !!}
        </div>
    </div>
    <div class="body">
        @foreach($records as $r)
            @if($linkDisable)
                <div class="hover:tw-shadow tw-rounded tw-px-1 tw-inline-block ub-text-default tw-h-10 tw-leading-10 tw-mr-2">
                    @if(!empty($r['logo']))
                        <img class="tw-h-10" src="{{$r['logo']}}" />
                    @else
                        {{$r['title']}}
                    @endif
                </div>
            @else
                <a href="{{$r['link']}}" target="_blank" class="hover:tw-shadow tw-rounded tw-px-1 tw-inline-block ub-text-default tw-h-10 tw-leading-10 tw-mr-2">
                    @if(!empty($r['logo']))
                        <img class="tw-h-10" data-src="{{$r['logo']}}" />
                    @else
                        {{$r['title']}}
                    @endif
                </a>
            @endif
        @endforeach
    </div>
</div>
