<div style="display:none;" data-grid-filter-field="{{$id}}"></div>
<script>
    (function () {
        var $field = $('[data-grid-filter-field={{$id}}]');
        $field.data('get', function () {
            var callback = $field.data('callback');
            if (callback) {
                return callback('get', $field);
            }
            return null;
        });
        $field.data('reset', function () {
            var callback = $field.data('callback');
            callback && callback('reset', $field);
        });
        $field.data('init', function (data) {
            var callback = $field.data('callback');
            callback && callback('reset', $field, data);
        });
    })();
</script>
