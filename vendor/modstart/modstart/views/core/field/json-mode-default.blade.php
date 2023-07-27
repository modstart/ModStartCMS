<input type="hidden"
       {{$readonly?'readonly':''}}
       class="form"
       id="{{$id}}Input"
       name="{{$name}}"
       placeholder="{{$placeholder}}"
       @if(null===$fixedValue)
       value="{{json_encode(null===$value?(null===$defaultValue?'':$defaultValue):$value,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)}}"
       @else
       value="{{json_encode($fixedValue?$fixedValue:'',JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)}}"
       @endif
       @if($styleFormField) style="{!! $styleFormField !!}" @endif
/>
<div class="pb-code-editor">
    <div id="{{$id}}Editor" style="width:100%;height:{{$editorHeight}};">{{json_encode(null===$value?(null===$defaultValue?new \stdClass():$defaultValue):$value,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)}}</div>
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
