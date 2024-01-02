<div class="line" data-field id="{{$id}}">
    <div class="label">
        {!! in_array('required',$rules)?'<span class="ub-text-danger ub-text-bold">*</span>':'' !!}
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}
    </div>
    <div class="field" >
        @if($readonly)
            <input type="hidden" name="{{$name}}" value="{{null===$value?$defaultValue:$value}}" />
        @endif
        <div class="layui-form tw-inline-block" lay-filter="{{$name}}">
            <select class="form" name="{{$name}}"
                    @if($selectSearch) lay-search @elseif($selectRemote) @else lay-ignore @endif
                    @if($selectRemote) lay-remote="{{$selectRemote}}" lay-init-value="{{null===$value?$defaultValue:$value}}" @endif
                    @if($readonly) disabled @endif
                    @if(!empty($onValueChangeJsFunction))
                        onchange="window.{{$id}}_change(this);"
                    @endif
            >
                @foreach($options as $k=>$v)
                    @if(isset($v['label']))
                        <option value="{{$k}}" @if((null===$value&&$k==$defaultValue)||(null!==$value&&$k==$value)) selected @endif @if(isset($v['title'])) title="{{$v['title']}}" @endif>{{$v['label']}}</option>
                    @else
                        <option value="{{$k}}" @if((null===$value&&$k==$defaultValue)||(null!==$value&&$k==$value)) selected @endif>{{$v}}</option>
                    @endif
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
