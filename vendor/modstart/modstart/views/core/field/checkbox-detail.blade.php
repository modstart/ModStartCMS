<div class="line">
    <div class="label">
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}:
    </div>
    <div class="field">
        @foreach($value as $v)
            @if(isset($options[$v]))
                <span class="ub-tag">{{$options[$v]}}</span>
            @else
                <span class="ub-tag">{{$v}}</span>
            @endif
        @endforeach
    </div>
</div>
