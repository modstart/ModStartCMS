<div class="line">
    <div class="label">
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}:
    </div>
    <div class="field">
        <div class="ub-html" style="background:#FFF;border-radius:0.1rem;padding:0.2rem;">{{$value}}</div>
    </div>
</div>
