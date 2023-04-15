<div class="line" id="{{$id}}">
    <div class="label">
        {!! in_array('required',$rules)?'<span class="ub-text-danger ub-text-bold">*</span>':'' !!}
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}
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
        <div id="{{$id}}Editor" style="width:100%;height:{{$editorHeight}};">{{empty($value)?'':json_encode($value,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)}}</div>
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
                $('[name={{$name}}]').data('editor',editor);
                //editor.setReadOnly(true);
            });
        </script>
    </div>
</div>
