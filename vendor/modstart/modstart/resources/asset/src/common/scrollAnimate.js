$(function () {
    var doAnimate = function () {
        var top = $(window).scrollTop() + $(window).height();
        $('[data-scroll-animate]').each(function (i, o) {
            if ($(o).offset().top < top) {
                $(o).addClass($(o).attr('data-scroll-animate'));
                $(o).removeAttr('data-scroll-animate');
            }
        });
    };
    $(window).on('scroll', function () {
        doAnimate();
    });
    doAnimate();
});
