<input type="hidden"
       {{$readonly?'readonly':''}}
       class="form"
       id="{{$id}}Input"
       name="{{$name}}"
       placeholder="{{$placeholder}}"
       @if(null===$fixedValue)
       value="{{\ModStart\Core\Util\SerializeUtil::jsonEncode(null===$value?(null===$defaultValue?'':$defaultValue):$value)}}"
       @else
       value="{{\ModStart\Core\Util\SerializeUtil::jsonEncode($fixedValue?$fixedValue:'')}}"
       @endif
       @if($styleFormField) style="{!! $styleFormField !!}" @endif
/>
<div class="pb-code-editor">
    <div id="{{$id}}Editor" style="width:100%;height:{{$editorHeight}};">{{\ModStart\Core\Util\SerializeUtil::jsonEncodePretty(null===$value?(null===$defaultValue?new \stdClass():$defaultValue):$value)}}</div>
</div>
<style>
    .pb-code-editor {
        border: 1px solid var(--color-body-line);
        border-radius: 0.2rem;
    }
    .pb-code-editor .ace_editor {
        border: none;
        border-radius: 0.1rem;
    }
</style>
{!! \ModStart\ModStart::js('asset/vendor/ace/ace.js') !!}
<script>
    $(function(){
        var editor = ace.edit("{{$id}}Editor");
        // editor.setTheme("ace/theme/monokai");
        editor.session.setMode("ace/mode/json");
        editor.setOptions({
            minLines: 3,
            maxLines: 20
        });
        editor.session.on('change',function(){
            $('#{{$id}}Input').val(editor.session.getValue());
        })
        $('#{{$id}}Input').data('editor',editor);
        //editor.setReadOnly(true);
    });
</script>
