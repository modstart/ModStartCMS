import {CountUp} from './../vendor/countUp.js';

if (!('MS' in window)) {
    window.MS = {};
}

var run = function () {
    $('[data-count-up-number]:visible')
        .each(function (i, o) {
            var $o = $(o);
            if ($o.is('[data-inited]')) {
                return;
            }
            if (!$o.isInViewport()) {
                return;
            }
            // console.log('visible', o);
            $o.attr('data-inited', '1');
            var value = parseInt($(o).attr('data-count-up-number'));
            var ins = new CountUp(o, value);
            if (ins.error) {
                $(o).html(value);
                return;
            }
            ins.start();
        });
};

$(document).on('scroll', function () {
    run();
});
$(function () {
    run();
});

window.MS.countUp = CountUp
