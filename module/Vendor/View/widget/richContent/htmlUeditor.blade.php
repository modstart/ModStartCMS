<script type="text/plain" id="{{$name}}" name="{{$name}}">{!! empty($value)?'':$value !!}</script>
<script src="@asset('asset/common/editor.js')"></script>
<script>
    $(function () {
        window.api.editor.basic('{{$name}}',{
            server:"{{modstart_web_url('member_data/ueditor')}}"
        });
    });
</script>
