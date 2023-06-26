<div class="line" id="{{$id}}">
    <div class="label">
        {!! in_array('required',$rules)?'<span class="ub-text-danger ub-text-bold">*</span>':'' !!}
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}
    </div>
    <div class="field" >
        @if($readonly)
            <input type="hidden" name="{{$name}}" value="{{null==$value?$defaultValue:$value}}" />
        @endif
        <select class="form" name="{{$name}}" lay-filter="{{$id}}Select"
                @if($readonly) disabled @endif
                @if(!empty($onValueChangeJsFunction))
                    onchange="window.{{$id}}_change(this);"
                @endif
        >
            @foreach($valueMap as $k=>$v)
                <option value="{{$k}}" @if((null===$value&&$k==$defaultValue)||(null!==$value&&$k==$value)) selected @endif>{{$v}}</option>
            @endforeach
        </select>
        @if(!empty($help))
            <div class="help">{!! $help !!}</div>
        @endif
    </div>
</div>
<script>
    @if(!empty($onValueChangeJsFunction))
        window.{{$id}}_change = function(o){
            ({!! $onValueChangeJsFunction !!})(o.options[o.selectedIndex].value);
        };
    @endif
</script>
