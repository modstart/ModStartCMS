<div class="line" id="{{$id}}">
    <div class="label">
        {!! str_contains($rules,'required')?'<span class="ub-text-danger ub-text-bold">*</span>':'' !!}
        {{$label}}:
    </div>
    <div class="field">
        <input type="text"
               {{$readonly?'readonly':''}}
               class="form"
               style="width:12em;"
               name="{{$name}}"
               id="{{$id}}Input"
               placeholder="{{$placeholder}}"
               autocomplete="off"
               value="{{$value}}" />
        @if(!empty($help))
            <div class="help">{!! $help !!}</div>
        @endif
    </div>
</div>
<script>
    layui.use('laydate', function () {
        var laydate = layui.laydate;
        laydate.render({
            elem: '#{{$id}}Input'
        });
    });
</script>
