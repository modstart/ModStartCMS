$(function () {
    $('[name=pid] option').each(function (i, o) {
        var $o = $(o), value = $o.attr('value');
        if (__navPositionMap[value]) {
            $o.html(__navPositionMap[value]);
        }
    });
    var pidChange = function () {
        var pid = parseInt($('[name=pid]').val())
        if (pid > 0) {
            $('[name="position"]').closest('.line').hide();
        } else {
            $('[name="position"]').closest('.line').show();
        }
    };
    $('[name=pid]').on('change', pidChange);
    pidChange();
});