<div class="line" id="{{$id}}">
    <div class="label">
        {!! in_array('required',$rules)?'<span class="ub-text-danger ub-text-bold">*</span>':'' !!}
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}
    </div>
    <div class="field">
        <div>
            <input type="text"
                   {{$readonly?'readonly':''}}
                   class="form"
                   style="width:8em;vertical-align:middle;"
                   name="{{$name}}"
                   placeholder="{{$placeholder}}"
                   value="{{$value}}" />
            <div id="{{$id}}Color" style="vertical-align:top;"></div>
        </div>
        @if(!empty($help))
            <div class="help">{!! $help !!}</div>
        @endif
    </div>
</div>
<script>
    layui.use('colorpicker', function () {
        var colorpicker = layui.colorpicker;
        colorpicker.render({
            elem: '#{{$id}}Color',
            color: '{{$value}}',
            done: function (color) {
                $('[name={{$name}}]').val(color);
            }
        });
    });
</script>
