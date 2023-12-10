<div class="line">
    <div class="label">
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}
    </div>
    <div class="field">
        <div style="box-shadow:0 0 2px #EEE;background:#FFF;border-radius:3px;padding:0 0 10px 0;">
            <div id="{{$name}}Tree"></div>
        </div>
    </div>
</div>
@include('modstart::core.field.tree-render',['renderId'=>"{$name}Tree"])
