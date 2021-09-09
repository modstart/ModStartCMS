<div class="line">
    <div class="label">
        {!! str_contains($rules,'required')?'<span class="ub-text-danger ub-text-bold">*</span>':'' !!}
        {{$label}}:
    </div>
    <div class="field">
        <input type="number"
               {{$readonly?'readonly':''}}
               class="form"
               step="any"
               name="{{$name}}"
               placeholder="{{$placeholder}}"
               value="{{$value}}" />
        @if(!empty($help))
            <div class="help">{!! $help !!}</div>
        @endif
    </div>
</div>
