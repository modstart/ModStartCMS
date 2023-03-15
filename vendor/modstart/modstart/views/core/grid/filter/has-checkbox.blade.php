<div class="field auto" data-grid-filter-field="{{$id}}">
    <div class="name">{{$label}}</div>
    <div class="input">
        @foreach($field->options() as $k=>$v)
            <label>
                 <input type="checkbox" @if(null!==$defaultValue&&in_array($k, $defaultValue)) checked @endif name="{{$id}}Checkbox" value="{{$k}}" />
                {{$v}}
            </label>
        @endforeach
    </div>
</div>
<script>
    (function () {
        var $field = $('[data-grid-filter-field={{$id}}]');
        $field.data('get', function () {
            var values = []
            $('[name="{{$id}}Checkbox"]:checked').each(function(i,o){
                values.push($(o).val());
            });
            return {
                '{{$column}}': {
                    has: values
                }
            };
        });
        $field.data('reset', function () {
            $('[name="{{$id}}Checkbox"]').prop('checked',false);
        });
        $field.data('init', function (data) {
            for (var i = 0; i < data.length; i++) {
                for (var k in data[i]) {
                    if (k === '{{$column}}' && ('has' in data[i][k])) {
                        $('[name="{{$id}}Checkbox"]').each(function(i,o){
                            $(o).prop('checked', data[i][k].has).includes($(o).val());
                        });
                    }
                }
            }
        });
    })();
</script>
