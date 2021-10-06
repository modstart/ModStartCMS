<script type="text/plain" id="{{$name}}" name="{{$name}}">{!! $value or '' !!}</script>
<script src="@asset('asset/common/editor.js')"></script>
<script>
    $(function () {
        window.api.editor.basic('{{$name}}');
    });
</script>
