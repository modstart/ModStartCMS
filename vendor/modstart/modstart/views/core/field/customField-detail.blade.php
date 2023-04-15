<div class="line">
    <div class="label">
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}
    </div>
    <div class="field">
        @if(!empty($value['type']))
            @if($value['type']=='Text')
                <span class="ub-tag">{{L('Text')}}</span>
                <span class="ub-tag">{{$value['title']}}</span>
            @elseif($value['type']=='Radio')
                <span class="ub-tag">{{L('Radio')}}</span>
            @endif
        @else
            <span class="ub-empty">无</span>
        @endif
    </div>
</div>


