<div class="line">
    <div class="label">
        {!! str_contains($rules,'required')?'<span class="ub-text-danger ub-text-bold">*</span>':'' !!}
        {{$label}}:
    </div>
    <div class="field">
        <textarea name="{{$name}}" rows="3"
                  placeholder="{{$placeholder}}"
                  @if($styleFormField) style="{!! $styleFormField !!}" @endif
                   {{$readonly?'readonly':''}}>{{$value}}</textarea>
        @if(!empty($help))
            <div class="help">{!! $help !!}</div>
        @endif
    </div>
</div>
