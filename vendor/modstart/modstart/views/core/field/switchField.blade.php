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
            <input type="checkbox" value="1" name="{{$name}}" lay-filter="checkbox-{{$name}}" lay-skin="switch" lay-text="{!! join('|',array_values($options)) !!}"  @if( (null===$value&&$defaultValue) || $value ) checked @endif />
            <input type="hidden" name="{{$name}}" value="0" />
        </div>
        @if(!empty($help))
            <div class="help">{!! $help !!}</div>
        @endif
    </div>
</div>
<script>
    layui.use('form', function () {
        layui.form.render('checkbox','{{$name}}');
        var change = function(){
            $('[type=hidden][name={{$name}}]').prop('disabled',$('[type=checkbox][name={{$name}}]').is(':checked'));
        };
        layui.form.on('switch(checkbox-{{$name}})', change);
        change();
    });
</script>
