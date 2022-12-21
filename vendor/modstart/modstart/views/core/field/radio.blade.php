<div class="line" id="{{$id}}">
    <div class="label">
        {!! str_contains($rules,'required')?'<span class="ub-text-danger ub-text-bold">*</span>':'' !!}
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}:
    </div>
    <div class="field layui-form" lay-filter="{{$id}}Radio">
        @foreach($options as $k=>$v)
            <input type="radio" value="{{$k}}" name="{{$name}}" @if(($value && $k==$value)||(!$value && $k==$defaultValue)) checked @endif title="{{$v}}">
        @endforeach
        @if(!empty($help))
            <div class="help">{!! $help !!}</div>
        @endif
    </div>
</div>
<script>
    layui.use('form', function () {
        var form = layui.form;
        form.render('radio','{{$id}}Radio');
    });
</script>
