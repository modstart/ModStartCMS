<div class="line" id="{{$id}}">
    <div class="label">
        {!! str_contains($rules,'required')?'<span class="ub-text-danger ub-text-bold">*</span>':'' !!}
        {{$label}}:
    </div>
    <div class="field">
        <input type="hidden"
               {{$readonly?'readonly':''}}
               class="form"
               name="{{$name}}"
               placeholder="{{$placeholder}}"
               @if(null===$fixedValue)
               value="{{empty($value)?'':json_encode($value,JSON_UNESCAPED_UNICODE)}}"
               @else
               value="{{empty($fixedValue)?'':json_encode($fixedValue,JSON_UNESCAPED_UNICODE)}}"
               @endif
               @if($styleFormField) style="{!! $styleFormField !!}" @endif
        />
        <div id="{{$id}}Editor" style="width:100%;height:200px;">{{empty($value)?'':json_encode($value,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)}}</div>
        @if(!empty($help))
            <div class="help">{!! $help !!}</div>
        @endif
        {!! \ModStart\ModStart::js('asset/vendor/ace/ace.js') !!}
        <script>
            $(function(){
                var editor = ace.edit("{{$id}}Editor");
                editor.setTheme("ace/theme/monokai");
                editor.session.setMode("ace/mode/json");
                editor.session.on('change',function(){
                    $('[name={{$name}}]').val(editor.session.getValue());
                })
                //editor.setReadOnly(true);
            });
        </script>
    </div>
</div>
