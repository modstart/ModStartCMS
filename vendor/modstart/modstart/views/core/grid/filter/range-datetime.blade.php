<div class="field" data-grid-filter-field="{{$id}}">
    <div class="name">{{$label}}</div>
    <div class="input">
        <input type="text" class="form" data-min value="{{empty($defaultValue['min'])?'':$defaultValue['min']}}"
               style="width:11em;"/>
        -
        <input type="text" class="form" data-max value="{{empty($defaultValue['max'])?'':$defaultValue['max']}}"
               style="width:11em;"/>
        @if(!empty($quickSelect))
            <div class="btn-group">
                @foreach($quickSelect as $q)
                    <a href="javascript:;" class="btn"
                       data-value-set
                       data-value-min="{{$q['min']}}"
                       data-value-max="{{$q['max']}}">
                        {{$q['label']}}
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
<script>
    (function () {
        var $field = $('[data-grid-filter-field={{$id}}]');
        var $valueSets = $field.find('[data-value-set]');
        var $min = $field.find('[data-min]');
        var $max = $field.find('[data-max]');
        var $inputs = [$min, $max];
        var autoFocusValueSet = function () {
            $valueSets
                .removeClass('ub-text-primary')
                .filter('[data-value-min="' + $min.val() + '"][data-value-max="' + $max.val() + '"]')
                .addClass('ub-text-primary');
        };
        layui.use('laydate', function () {
            for (var i = 0; i < $inputs.length; i++) {
                (function ($input) {
                    layui.laydate.render({
                        elem: $input.get(0),
                        type: 'datetime',
                        done: function (value, date, endDate) {
                            $input.trigger('change');
                        }
                    });
                    $input.on('change', function () {
                        autoFocusValueSet();
                    });
                })($inputs[i]);
            }
        });
        $valueSets.on('click', function () {
            $min.val($(this).data('value-min'));
            $max.val($(this).data('value-max'));
            autoFocusValueSet();
        });
        $field.data('get', function () {
            return {
                '{{$column}}': {
                    range: {
                        min: $min.val(),
                        max: $max.val()
                    }
                }
            };
        });
        $field.data('reset', function () {
            $min.val('');
            $max.val('');
            autoFocusValueSet();
        });
        $field.data('init', function (data) {
            for (var i = 0; i < data.length; i++) {
                for (var k in data[i]) {
                    if (k === '{{$column}}' && ('range' in data[i][k])) {
                        if ('min' in data[i][k].range) {
                            $min.val(data[i][k].range.min)
                        }
                        if ('max' in data[i][k].range) {
                            $max.val(data[i][k].range.max)
                        }
                    }
                }
            }
        });
    })();
</script>
