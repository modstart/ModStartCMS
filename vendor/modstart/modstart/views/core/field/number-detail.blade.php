<div class="line">
    <div class="label">
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}
    </div>
    <div class="field">
        <div class="value">
            @if($autoColor)
                <span class="ub-text-{{$value>0?'success':'danger'}}">{{($signShow&&($value>0))?'+':''}}{{$value}}</span>
            @else
                {{($signShow&&($value>0))?'+':''}}{{$value}}
            @endif
            {{empty($unit)?'':$unit}}
        </div>
        <input type="hidden" name="{{$name}}" value="{{$value}}"/>
    </div>
</div>
