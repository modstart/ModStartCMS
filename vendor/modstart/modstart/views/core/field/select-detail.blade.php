<div class="line">
    <div class="label">
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}:
    </div>
    <div class="field">
        @if(isset($options[$value]))
            {{$options[$value]}}
        @else
            @if($value)
                {{$value}}
            @else
                <span class="ub-text-muted">-</span>
            @endif
        @endif
    </div>
</div>
