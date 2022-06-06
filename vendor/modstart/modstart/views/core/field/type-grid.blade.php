<?php $valueLabel = isset($valueMap[$value])?$valueMap[$value]:$value; ?>
@if($gridEditable)
    <select lay-ignore data-type="{{$name}}_{{$_index}}" class="form ub-text-{{isset($colorMap[$value])?$colorMap[$value]:'default'}}">
        @foreach($valueMap as $k=>$v)
            <option value="{{$k}}" @if($value==$k) selected @endif>
                {{$v}}
            </option>
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
    <span class="ub-text-{{isset($colorMap[$value])?$colorMap[$value]:'default'}}">
        <i class="iconfont icon-dot-sm ub-text-{{isset($colorMap[$value])?$colorMap[$value]:'default'}}"></i>{{$valueLabel}}
    </span>
@endif
