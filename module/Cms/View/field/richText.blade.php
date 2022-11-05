<script type="text/plain" id="{{$field['name']}}"
        name="{{$field['name']}}">{!! $record?$record[$field['name']]:'' !!}</script>
{!! \ModStart\ModStart::js('asset/common/editor.js') !!}
<script>
    $(function () {
        window.api.editor.basic('{{$field['name']}}', {
            server: "{{modstart_web_url('member_data/ueditor')}}",
            ready: function () {
                // console.log('ready');
            }
        }, {topOffset: 0});
    });
</script>
