<div class="line" data-field="{{$name}}">
    <div class="label">
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}
    </div>
    <div class="field">
        @if(isset($options[$value]))
            {{$options[$value]}}
        @else
            <span class="ub-text-muted">-</span>
        @endif
    </div>
</div>
