@if(!empty($colorMap))
    <span>
        <i class="iconfont icon-dot-sm ub-text-{{isset($colorMap[$value])?$colorMap[$value]:'default'}}"></i>
        @if(isset($valueMap[$value]))
            {{$valueMap[$value]}}
        @else
            {{$value}}
        @endif
    </span>
@else
    @if(isset($valueMap[$value]))
        {{$valueMap[$value]}}
    @else
        {{$value}}
    @endif
@endif