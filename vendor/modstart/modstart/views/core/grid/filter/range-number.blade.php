<div class="field" data-grid-filter-field="{{$id}}">
    <div class="name">{{$label}}</div>
    <div class="input">
        <input type="number" class="form" data-min value="{{empty($defaultValue['min'])?'':$defaultValue['min']}}" style="width:8em;"/>
        -
        <input type="number" class="form" data-max value="{{empty($defaultValue['max'])?'':$defaultValue['max']}}" style="width:8em;"/>
    </div>
</div>
<script>
    (function () {
        var $field = $('[data-grid-filter-field={{$id}}]');
        $field.data('get', function () {
            return {
                '{{$column}}': {
                    range: {
                        min: $field.find('[data-min]').val(),
                        max: $field.find('[data-max]').val()
                    }
                }
            };
        });
        $field.data('reset', function () {
            $('[data-grid-filter-field={{$id}}] input').val('');
        });
        $field.data('init', function (data) {
            for (var i = 0; i < data.length; i++) {
                for (var k in data[i]) {
                    if (k === '{{$column}}' && ('range' in data[i][k])) {
                        if ('min' in data[i][k].range) {
                            $field.find('[data-min]').val(data[i][k].range.min)
                        }
                        if ('max' in data[i][k].range) {
                            $field.find('[data-max]').val(data[i][k].range.max)
                        }
                    }
                }
            }
        });
    })();
</script>
