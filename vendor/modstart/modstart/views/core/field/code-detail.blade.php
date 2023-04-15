<div class="line">
    <div class="label">
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}
    </div>
    <div class="field">
        @if(!empty($value))
            <pre style="margin:0;overflow:auto;width:{{$width}};max-height:{{$maxHeight}}">{{$value}}</pre>
        @endif
    </div>
</div>
