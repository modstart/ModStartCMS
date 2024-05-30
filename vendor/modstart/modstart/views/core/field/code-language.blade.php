<input type="hidden"
       {{$readonly?'readonly':''}}
       class="form"
       id="{{$id}}Input"
       name="{{$name}}"
       value="{{null===$value?(null===$defaultValue?'':$defaultValue):$value}}"
       @if($styleFormField) style="{!! $styleFormField !!}" @endif
/>
<div class="pb-code-editor">
    <div id="{{$id}}Editor" style="width:100%;height:{{$editorHeight}};">{{null===$value?(null===$defaultValue?'':$defaultValue):$value}}</div>
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
        editor.session.setMode("ace/mode/{{$language}}");
        editor.setOptions({
            minLines: 3,
            maxLines: 20,
            fontSize: "var(--font-size)"
        });
        editor.session.on('change',function(){
            $('#{{$id}}Input').val(editor.session.getValue());
        })
        $('#{{$id}}Input').data('editor',editor);
        //editor.setReadOnly(true);
    });
</script>
