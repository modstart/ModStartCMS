<div class="line" data-field="{{$name}}">
    <div class="label">
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}
    </div>
    <div class="field">
        <div class="ub-html" style="padding:0.25rem 0;">{!! \ModStart\Core\Util\HtmlUtil::text2html($value) !!}</div>
    </div>
</div>
