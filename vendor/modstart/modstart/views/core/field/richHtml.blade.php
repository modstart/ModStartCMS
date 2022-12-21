<div class="line" id="{{$id}}">
    <div class="label">
        {!! str_contains($rules,'required')?'<span class="ub-text-danger ub-text-bold">*</span>':'' !!}
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}:
    </div>
    <div class="field">
        <div>
            <textarea id="{{$id}}Editor" name="{{$name}}" style="height:0px;overflow:hidden;">{!! htmlspecialchars($value) !!}</textarea>
        </div>
        @if(!empty($help))
            <div class="help">{!! $help !!}</div>
        @endif
    </div>
</div>
<script>
    $(function () {
        @if($editorMode=='simple')
        window.api.editor.simple('{{$id}}Editor', {
            server: "{{$server}}",
            ready: function () {
                $('#{{$id}}').trigger('editor-ready');
            }
        }, {topOffset: 0});
        @else
        window.api.editor.basic('{{$id}}Editor', {
            server: "{{$server}}",
            ready: function () {
                $('#{{$id}}').trigger('editor-ready');
            }
        }, {topOffset: 0});
        @endif
    });
</script>
@foreach(\ModStart\Field\Plugin\RichHtmlPlugin::all() as $plugin)
    {!! $plugin->render([
        'currentApp'=>\Illuminate\Support\Facades\Session::get('_currentApp'),
        'fieldId'=>$id,
        'name'=>$name
    ]) !!}
@endforeach
