$(function () {
    $('[name="position"]').closest('.line').insertBefore($('[name="pid"]').closest('.line'));
    var positionChange = function () {
        var position = $('[name=position]').val();
        var oldPid = $('[name=pid]').val();
        $('[name=pid]').html('');
        $('[name=pid]').append('<option value="0">顶级</option>');
        for (var i in window.__positionNavs) {
            var nav = window.__positionNavs[i];
            if (nav.position !== position) {
                continue;
            }
            $('[name=pid]').append('<option value="' + nav.id + '">'
                // + nav.position + ' '
                + MS.util.specialchars(nav.name) + '</option>');
        }
        $('[name=pid]').val(oldPid);
        if (null === $('[name=pid]').val()) {
            $('[name=pid]').val(0);
        }
    };
    $('[name=position]').on('change', positionChange);
    positionChange();
});
