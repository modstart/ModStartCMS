<div class="line" id="{{$id}}">
    <div class="label">
        {!! in_array('required',$rules)?'<span class="ub-text-danger ub-text-bold">*</span>':'' !!}
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}
    </div>
    <div class="field layui-form" lay-filter="{{$name}}">
        @foreach($options as $k=>$v)
            @if(!empty($vertical)) <div> @endif
            <input type="radio" value="{{$k}}" name="{{$name}}" @if((null===$value&&$k==$defaultValue)||(null!==$value&&$k==$value)) checked @endif title="{{$v}}" />
            @if(!empty($vertical)) </div> @endif
        @endforeach
        @if(!empty($help))
            <div class="help">{!! $help !!}</div>
        @endif
    </div>
</div>
<script>
    layui.use('form', function () {
        layui.form.render('radio','{{$id}}Radio');
    });
</script>
