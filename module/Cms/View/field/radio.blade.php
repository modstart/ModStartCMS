@if(!empty($field['fieldData']['options']))
    @foreach($field['fieldData']['options'] as $option)
        <label>
            <input type="radio" name="{{$field['name']}}" value="{{$option}}"
                    {{$record&&$record[$field['name']]==$option?'checked':''}} />
            {{$option}}
        </label>
    @endforeach
@endif
