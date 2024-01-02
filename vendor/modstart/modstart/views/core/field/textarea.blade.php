<div class="line" data-field id="{{$id}}">
    <div class="label">
        {!! in_array('required',$rules)?'<span class="ub-text-danger ub-text-bold">*</span>':'' !!}
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i
                    class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}
    </div>
    <div class="field">
        <textarea name="{{$name}}" rows="3"
                  placeholder="{{$placeholder}}"
                  @if($styleFormField) style="{!! $styleFormField !!}" @endif
            {{$readonly?'readonly':''}}>{{null==$value?$defaultValue:$value}}</textarea>
        @if($autoHeight)
            <script>
                $(function () {
                    var $field = $('#{{$id}}');
                    var resize = function () {
                        this.style.height = "auto";
                        this.style.height = Math.max(this.scrollHeight, {{$autoHeightMin}}) + "px";
                    };
                    $field.find('textarea').on('input', resize);
                    resize.call($field.find('textarea')[0]);
                });
            </script>
        @endif
        @if(!empty($help))
            <div class="help">{!! $help !!}</div>
        @endif
    </div>
</div>
