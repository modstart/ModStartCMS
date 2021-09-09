<div class="line" id="{{$id}}">
    <div class="label">
        {!! str_contains($rules,'required')?'<span class="ub-text-danger ub-text-bold">*</span>':'' !!}
        {{$label}}:
    </div>
    <div class="field layui-form" lay-filter="{{$name}}">
        @foreach($options as $k=>$v)
            <input type="checkbox" value="{{$k}}" name="{{$name}}[]" lay-skin="primary" @if($value && in_array($k,$value)) checked @endif title="{{$v}}">
        @endforeach
        @if(!empty($help))
            <div class="help">{!! $help !!}</div>
        @endif
    </div>
</div>
<script>
    layui.use('form', function () {
        var form = layui.form;
        form.render('checkbox','{{$name}}');
    });
</script>