<div class="line">
    <div class="label">
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}
    </div>
    <div class="field">
        @if($value)
            <a href="{{\ModStart\Core\Assets\AssetsUtil::fix($value)}}"
               style="display:inline-block;box-sizing:border-box;" data-image-preview>
                <img src="{{\ModStart\Core\Assets\AssetsUtil::fix($value)}}" style="max-height:2rem;max-width:2rem;display:inline-block;box-shadow:0 0 1px #CCC;" />
            </a>
        @else
            <span class="ub-text-muted">-</span>
        @endif
    </div>
</div>
