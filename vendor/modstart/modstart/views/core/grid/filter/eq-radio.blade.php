<div class="field" data-grid-filter-field="{{$id}}">
    <div class="name">{{$label}}</div>
    <div class="input">
        <label>
            <input type="radio" name="{{$id}}" @if(null===$defaultValue) checked @endif value=""/>
            {{L('All')}}
        </label>
        @foreach($field->options() as $k=>$v)
            <label>
                <input type="radio" name="{{$id}}" value="{{$k}}" @if(null!==$defaultValue&&$defaultValue==$k) checked @endif />
                {{$v}}
            </label>
        @endforeach
    </div>
</div>
<script>
    (function () {
        var $field = $('[data-grid-filter-field={{$id}}]');
        $field.data('get', function () {
            return {
                '{{$column}}': {
                    eq: $('[name={{$id}}]:checked').val()
                }
            };
        });
        $field.data('reset', function () {
            $('[name={{$id}}]:first').prop('checked', true);
        });
        $field.data('init', function (data) {
            for (var i = 0; i < data.length; i++) {
                for (var k in data[i]) {
                    if (k === '{{$column}}' && ('eq' in data[i][k])) {
                        if (data[i][k].eq) {
                            $('[name={{$id}}][value=' + data[i][k].eq + ']').prop('checked', true);
                        }
                    }
                }
            }
        });
    })();
</script>
