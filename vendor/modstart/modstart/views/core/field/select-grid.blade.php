@if(!empty($gridEditable))
    <select lay-ignore data-type="{{$name}}_{{$_index}}" class="form-sm">
        @foreach($options as $k=>$v)
            @if(isset($v['label']))
                @if(isset($v['title']))
                    <option value="{{$v['label']}}" @if($value==$k) selected @endif>{{$v['title']}}</option>
                @else
                    <option value="{{$v['label']}}" @if($value==$k) selected @endif>{{$v['label']}}</option>
                @endif
            @else
                <option value="{{$k}}" @if($value==$k) selected @endif>{{$v}}</option>
            @endif
        @endforeach
    </select>
    <script>
        $('[data-type={{$name}}_{{$_index}}]').off('change').on('change',function(){
            var data = {
                ele: this,
                index: {{$_index}},
                column: '{{$column}}',
                value: $(this).val()
            };
            $(this).closest('[data-grid]').trigger('grid-item-cell-change', data);
        });
    </script>
@else
    @if(isset($options[$value]))
        @if(isset($options[$value]['label']))
            @if(isset($options[$value]['title']))
                {{$options[$value]['title']}}
            @else
                {{$options[$value]['label']}}
            @endif
        @else
            {{$options[$value]}}
        @endif
    @else
        @if($value)
            {{$value}}
        @else
            <span class="ub-text-muted">-</span>
        @endif
    @endif
@endif
