@if(null!==$label)
<div class="line" id="{{$id}}">
    <div class="label">
        {!! str_contains($rules,'required')?'<span class="ub-text-danger ub-text-bold">*</span>':'' !!}
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}:
    </div>
    <div class="field">
@endif
        <div class="layui-form" lay-filter="{{$name}}">
            @foreach($options as $k=>$v)
                <input type="checkbox" value="{{$k}}" name="{{$name}}[]" lay-skin="primary" @if($value && in_array($k,$value)) checked @endif title="{{$v}}">
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
