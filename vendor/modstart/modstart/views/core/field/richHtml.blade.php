<div class="line" data-field id="{{$id}}">
    <div class="label">
        {!! in_array('required',$rules)?'<span class="ub-text-danger ub-text-bold">*</span>':'' !!}
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}
    </div>
    <div class="field">
        <div>
            <textarea id="{{$id}}Editor" name="{{$name}}" style="height:0px;overflow:hidden;">{!! htmlspecialchars(null===$value?$defaultValue:$value) !!}</textarea>
        </div>
        @if(!empty($help))
            <div class="help">{!! $help !!}</div>
        @endif
    </div>
</div>
<script>
    $(function () {
        var option = {
            topOffset: 0
        };
        @if(!empty($editorOption));
            option = Object.assign(option,{!! \ModStart\Core\Util\SerializeUtil::jsonEncode($editorOption) !!});
        @endif
        @if($editorMode=='simple')
            window.api.editor.simple('{{$id}}Editor', {
                server: "{{$server}}",
                ready: function () {
                    $('#{{$id}}').trigger('editor-ready');
                }
            }, option);
        @else
            window.api.editor.basic('{{$id}}Editor', {
                server: "{{$server}}",
                ready: function () {
                    $('#{{$id}}').trigger('editor-ready');
                }
            }, option);
        @endif
    });
</script>
@foreach(\ModStart\Field\Plugin\RichHtmlPlugin::all() as $plugin)
    {!! $plugin->render([
        'currentApp'=>\ModStart\App\Core\CurrentApp::get(),
        'fieldId'=>$id,
        'name'=>$name
    ]) !!}
@endforeach
