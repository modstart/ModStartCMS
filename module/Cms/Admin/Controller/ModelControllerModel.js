$(function () {
    var pinyin = window.MS.vendor.pinyin;
    var $name = $('input[name=name]'), $title = $('input[name=title]');
    var generate = function () {
        if ($name.prop('readonly')) {
            return;
        }
        var v = $title.val();
        if (!v) {
            return;
        }
        var value, s, i;
        value = pinyin(v, {
            style: pinyin.STYLE_NORMAL,
        });
        s = [];
        for (i = 0; i < value.length; i++) {
            s.push(value[i].join(''));
        }
        s = s.join('');
        if (s.length > 50) {
            value = pinyin(v, {
                style: pinyin.STYLE_INITIALS,
            });
            s = [];
            for (i = 0; i < value.length; i++) {
                s.push(value[i].join(''));
            }
            s = s.join('');
        }
        $name.val(s);
    }
    $name.on('focus', function () {
        if (!$name.val()) {
            generate();
        }
    });
    $('[data-name-generate]').on('click', generate);
});
