<div class="line">
    <div class="label">
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        @if(!empty($label))
            {{$label}}:
        @endif
    </div>
    <div class="field">
        {!! $value !!}
    </div>
</div>
