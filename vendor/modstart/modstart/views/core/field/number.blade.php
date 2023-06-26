@if(null!==$label)
<div class="line">
    <div class="label">
        {!! in_array('required',$rules)?'<span class="ub-text-danger ub-text-bold">*</span>':'' !!}
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}
    </div>
    <div class="field">
@endif
    <input type="number"
           {{$readonly?'readonly':''}}
           class="form"
           step="any"
           name="{{$name}}"
           placeholder="{{$placeholder}}"
           value="{{null==$value?$defaultValue:$value}}" />
        {{empty($unit)?'':$unit}}
        @if(!empty($help))
            <div class="help">{!! $help !!}</div>
        @endif
@if(null!==$label)
    </div>
</div>
@endif
