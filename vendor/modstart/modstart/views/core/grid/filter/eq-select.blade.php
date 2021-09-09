<div class="field" data-grid-filter-field="{{$id}}">
    <div class="name">{{$label}}</div>
    <div class="input">
        <select class="form" id="{{$id}}_select">
            <option value="">{{L('All')}}</option>
            @foreach($field->options() as $k=>$v)
                <option value="{{$k}}">{{$v}}</option>
            @endforeach
        </select>
    </div>
</div>
<script>
    (function () {
        var $field = $('[data-grid-filter-field={{$id}}]');
        $field.data('get', function () {
            return {
                '{{$column}}': {
                    eq: $('#{{$id}}_select').val()
                }
            };
        });
        $field.data('reset', function () {
            $('#{{$id}}_select').val('');
        });
        $field.data('init', function (data) {
            for (var i = 0; i < data.length; i++) {
                for (var k in data[i]) {
                    if (k === '{{$column}}' && ('eq' in data[i][k])) {
                        $('#{{$id}}_select').val(data[i][k].eq);
                    }
                }
            }
        });
    })();
</script>