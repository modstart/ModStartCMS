<div class="line">
    <div class="label">
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}:
    </div>
    <div class="field">
        @if(!empty($colorMap))
            <span class="ub-text-{{isset($colorMap[$value])?$colorMap[$value]:'default'}}">
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
    </div>
</div>
