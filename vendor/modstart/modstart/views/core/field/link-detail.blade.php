<div class="line" data-field="{{$name}}">
    <div class="label">
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}
    </div>
    <div class="field">
        <div class="value">
            @if(!empty($value))
                <a href="{{$value}}" target="_blank">{{$value}}</a>
            @endif
        </div>
    </div>
</div>
