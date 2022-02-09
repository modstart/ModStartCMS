<div class="field" data-grid-filter-field="{{$id}}">
    <div class="name">{{$label}}</div>
    <div class="input">
        <input type="text" class="form" value="{{empty($defaultValue)?'':$defaultValue}}" name="{{$id}}"/>
    </div>
</div>
<script>
    (function () {
        var $field = $('[data-grid-filter-field={{$id}}]');
        $field.data('get', function () {
            return {
                '{{$column}}': {
                    like: $('[data-grid-filter-field={{$id}}] input').val()
                }
            };
        });
        $field.data('reset', function () {
            $('[data-grid-filter-field={{$id}}] input').val('');
        });
        $field.data('init', function (data) {
            for (var i = 0; i < data.length; i++) {
                for (var k in data[i]) {
                    if (k === '{{$column}}' && ('like' in data[i][k])) {
                        $('[data-grid-filter-field={{$id}}] input').val(data[i][k].like);
                    }
                }
            }
        });
    })();
</script>
