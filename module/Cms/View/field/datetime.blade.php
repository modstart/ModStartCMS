<input type="text"
       class="form"
       style="width:12em;"
       name="{{$field['name']}}"
       id="{{$field['name']}}Input"
       autocomplete="off" />
<script>
    layui.use('laydate', function () {
        var laydate = layui.laydate;
        laydate.render({
            elem: '#{{$field['name']}}Input',
            type: 'datetime'
        });
    });
</script>
