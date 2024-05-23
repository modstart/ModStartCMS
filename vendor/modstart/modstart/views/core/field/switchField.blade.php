<div class="line" data-field="{{$name}}" id="{{$id}}">
    <div class="label">
        {!! in_array('required',$rules)?'<span class="ub-text-danger ub-text-bold">*</span>':'' !!}
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}
    </div>
    <div class="field layui-form" lay-filter="{{$name}}">
        <div style="margin-top:-4px;">
            <input type="checkbox" value="1" name="{{$name}}" lay-skin="switch" lay-text="{!! join('|',array_values($options)) !!}"  @if( (null===$value&&$defaultValue) || $value ) checked @endif />
        </div>
        @if(!empty($help))
            <div class="help">{!! $help !!}</div>
        @endif
    </div>
</div>
<script>
    layui.use('form', function () {
        layui.form.render('checkbox','{{$name}}');
    });
</script>
