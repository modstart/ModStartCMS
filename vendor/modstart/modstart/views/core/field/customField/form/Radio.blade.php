{{--delete at 2024-06-15--}}
<div class="line">
    <div class="label">
        {{$field['title']}}
    </div>
    <div class="field">
        @foreach($field['data']['option'] as $option)
            <div>
                <label class="tw-bg-white">
                    <input type="radio" name="{{$fieldName}}" value="{{$option}}"
                           @if($option==$value) checked @endif />
                    {{$option}}
                </label>
            </div>
        @endforeach
    </div>
</div>
