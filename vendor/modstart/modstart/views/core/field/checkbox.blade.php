@if(null!==$label)
<div class="line" data-field id="{{$id}}">
    <div class="label">
        {!! in_array('required',$rules)?'<span class="ub-text-danger ub-text-bold">*</span>':'' !!}
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}
    </div>
    <div class="field">
@endif
        <div class="layui-form" lay-filter="{{$name}}">
            @foreach($options as $k=>$v)
                <input type="checkbox" value="{{$k}}" name="{{$name}}[]" lay-skin="primary"
                       @if((null!==$value&&!empty($value)&&in_array($k,$value))||(null===$value&&!empty($defaultValue)&&in_array($k,$defaultValue))) checked @endif title="{{$v}}">
            @endforeach
        </div>
        @if(!empty($help))
            <div class="help">{!! $help !!}</div>
        @endif
@if(null!==$label)
    </div>
</div>
@endif
<script>
    layui.use('form', function () {
        var form = layui.form;
        form.render('checkbox','{{$name}}');
    });
</script>
