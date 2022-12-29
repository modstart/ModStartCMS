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
            var value = $(o).attr('data-count-up-number');
            value = value.replace(/,/g, '');
            // console.log('value', value);
            var opt = {}
            if (value) {
                if(value.indexOf('.') >= 0){
                    opt.decimalPlaces = value.split('.')[1].length;
                }
            }
            var ins = new CountUp(o, value, opt);
            // console.log('ins.error',ins.error)
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
