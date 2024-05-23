<div class="line" data-field="{{$name}}">
    <div class="label">
        {!! in_array('required',$rules)?'<span class="ub-text-danger ub-text-bold">*</span>':'' !!}
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}
    </div>
    <div class="field">
        @include('modstart::core.field.code-language')
        @if(!empty($help))
            <div class="help">{!! $help !!}</div>
        @endif
    </div>
    @if(!empty($editorScripts))
        {!! $editorScripts !!}
    @endif
</div>
