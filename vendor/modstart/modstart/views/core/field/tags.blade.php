<?php
$valueTags = [];
if(null===$value){
    if(!empty($defaultValue)){
        foreach ($defaultValue as $v) {
            $valueTags[]=(isset($tags[$v])?$tags[$v]:$v);
        }
    }
}else{
    foreach ($value as $v) {
        $valueTags[]=(isset($tags[$v])?$tags[$v]:$v);
    }
}
?>
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
               id="{{$id}}Tags"
               placeholder="{{$placeholder}}"
               value="{!! htmlspecialchars(json_encode($valueTags)) !!}" />
        @if(!empty($help))
            <div class="help">{!! $help !!}</div>
        @endif
    </div>
</div>
{!! \ModStart\ModStart::js('asset/vendor/tagify/jQuery.tagify.min.js') !!}
{!! \ModStart\ModStart::css('asset/vendor/tagify/tagify.css') !!}
<script>
    $(function () {
        var $field = $('#{{$id}}');
        var $tag = $('#{{$id}}Tags').tagify({
            whitelist : {!! json_encode(array_values($tags)) !!},
            dropdown: {
                maxItems: 20,
                classname: "tagify-dropdown-list",
                enabled: 0,
                closeOnSelect: false
            },
            originalInputValueFormat:function(valuesArr){
                var values = [];
                for(var i=0;i<valuesArr.length;i++){
                    values.push(valuesArr[i].value);
                }
                @if($serializeType===\ModStart\Field\Tags::SERIALIZE_TYPE_COLON_SEPARATED)
                    if(values.length>0){
                        return ':'+values.join('::')+':';
                    }
                    return '';
                @else
                    return JSON.stringify(values);
                @endif
            }
        });
    });
</script>
