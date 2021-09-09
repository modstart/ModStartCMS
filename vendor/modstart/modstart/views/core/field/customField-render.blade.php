<div class="line">
    <div class="label">
        {{$field['title']}}
    </div>
    <div class="field">
        @if($field['type']=='Text')
            <input type="text" class="form" name="{{$fieldName}}" />
        @elseif($field['type']=='Radio')
            @foreach($field['data']['option'] as $option)
                <div>
                    <label>
                        <input type="radio" name="{{$fieldName}}" value="{{$option}}" />
                        {{$option}}
                    </label>
                </div>
            @endforeach
        @endif
    </div>
</div>
