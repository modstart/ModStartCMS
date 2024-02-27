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
           @if(isset($min)&&null!==$min) min="{{$min}}" @endif
           @if(isset($max)&&null!==$max) max="{{$max}}" @endif
           @if(isset($step)&&null!==$step) step="{{$step}}" @endif
           value="{{null===$value?$defaultValue:$value}}" />
        @if(!empty($unit)&&(empty($unitPosition)||$unitPosition=='after'))
            {{$unit}}
        @endif
        @if(!empty($help))
            <div class="help">{!! $help !!}</div>
        @endif
@if(null!==$label)
    </div>
</div>
@endif
