<div class="line">
    <div class="label">
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}
    </div>
    <div class="field">
        <div class="tw-w-auto tw-break-normal tw-whitespace-normal">
        @foreach($value as $v)
            @if(isset($tags[$v]))
                <span class="ub-tag">{{$tags[$v]}}</span>
            @else
                <span class="ub-tag">{{$v}}</span>
            @endif
        @endforeach
        </div>
    </div>
</div>
