<div class="line" id="{{$id}}">
    <div class="label">
        {!! in_array('required',$rules)?'<span class="ub-text-danger ub-text-bold">*</span>':'' !!}
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}:
    </div>
    <div class="field" >
        @if($readonly)
            <input type="hidden" name="{{$name}}" value="{{$value}}" />
        @endif
        <div class="layui-form tw-inline-block" lay-filter="{{$name}}">
            <select class="form" name="{{$name}}"
                    @if($selectSearch) lay-search @else lay-ignore @endif
                    @if($readonly) disabled @endif
                    @if(!empty($onValueChangeJsFunction))
                        onchange="window.{{$id}}_change(this);"
                    @endif
            >
                @foreach($options as $k=>$v)
                    <option value="{{$k}}" @if($k==$value) selected @endif>{{$v}}</option>
                @endforeach
            </select>
        </div>
        @if(!empty($help))
            <div class="help">{!! $help !!}</div>
        @endif
    </div>
</div>
<script>
    layui.use('form', function(){
        layui.form.render('select','{{$name}}');
    });
    @if(!empty($onValueChangeJsFunction))
        window.{{$id}}_change = function(o){
            ({!! $onValueChangeJsFunction !!})(o.options[o.selectedIndex].value);
        };
    @endif
</script>
