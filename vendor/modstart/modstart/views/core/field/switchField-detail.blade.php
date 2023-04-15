<div class="line">
    <div class="label">
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}
    </div>
    <div class="field">
        @if(!empty($value))
            <span class="ub-text-success">{{$options[1]}}</span>
        @else
            <span class="ub-text-muted">{{$options[0]}}</span>
        @endif
    </div>
</div>
