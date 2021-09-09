<div class="line">
    <div class="label">
        {!! str_contains($rules,'required')?'<span class="ub-text-danger ub-text-bold">*</span>':'' !!}
        {{$label}}:
    </div>
    <div class="field">
        <input type="password"
               class="form"
               name="{{$name}}"
               placeholder="{{$placeholder}}"
               @if($styleFormField) style="{!! $styleFormField !!}" @endif
               value="{{$value}}" />
        @if(!empty($help))
            <div class="help">{!! $help !!}</div>
        @endif
    </div>
</div>
