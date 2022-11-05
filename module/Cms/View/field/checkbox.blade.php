<?php $values = json_decode($record?$record[$field['name']]:'[]',true); ?>
@foreach($field['fieldData']['options'] as $option)
    <label>
        <input type="checkbox" name="{{$field['name']}}" value="{{$option}}"
                {{in_array($option,$values)?'checked':''}} />
        {{$option}}
    </label>
@endforeach
