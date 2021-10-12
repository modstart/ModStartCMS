$(window).on('load', function () {
    /**
     * 修复部分依赖窗口滚动时间的组件（ueditor顶部悬浮工具栏)
     */
    $('.ub-panel-frame > .right > .content.fixed').scroll(function () {
        var evt = document.createEvent('HTMLEvents');
        evt.initEvent('scroll', false, true);
        window.dispatchEvent(evt);
    });
    var isMobile = ($(window).width() < 600)
    var $frame = $('.ub-panel-frame');
    if (isMobile) {
        $frame.find('.left-menu-shrink').on('click', function () {
            $frame.removeClass('left-toggle');
        });
        $frame.find('.left-trigger').on('click', function () {
            $frame.addClass('left-toggle');
        });
    } else {
        $frame.find('.left-trigger').on('click', function () {
            $frame.toggleClass('left-toggle');
            window.api.base.postSuccess(
                window.__msAdminRoot + "util/frame",
                {frameLeftToggle: $frame.is('.left-toggle')},
                function (res) {
                }
            );
        });
    }
});
