<select name="{{$field['name']}}">
    <option value="">[请选择]</option>
    @foreach($field['fieldData']['options'] as $option)
        <option value="{{$option}}" {{$record&&$record[$field['name']]==$option?'selected':''}}>
            {{$option}}
        </option>
    @endforeach
</select>
