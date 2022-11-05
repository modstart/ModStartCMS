<select name="{{$field['name']}}">
    @foreach($field['fieldData']['options'] as $option)
        <option value="{{$option}}" {{$record&&$record[$field['name']]==$option?'selected':''}}>
            {{$option}}
        </option>
    @endforeach
</select>
