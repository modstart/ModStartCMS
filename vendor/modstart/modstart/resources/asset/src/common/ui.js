window.MS = window.MS || {}
window.MS.ui = {
    tab: function (tabSelector, bodySelector, option) {
        var opt = $.extend({
            tabClass: 'active',
            bodyClass: 'ub-block',
        }, option);
        $(tabSelector).on('click', function () {
            var index = $(tabSelector).index(this);
            $(tabSelector).removeClass(opt.tabClass).eq(index).addClass(opt.tabClass);
            $(bodySelector).removeClass(opt.bodyClass).eq(index).addClass(opt.bodyClass);
            return false;
        });
    },
    tabScroller: function (tabSelector, bodySelector, option) {
        var opt = $.extend({
            tabActiveClass: 'active',
            bodyActiveClass: 'ub-block',
            scroller: window,
            scrollOffset: 0,
        }, option);
        $(tabSelector).on('click', function () {
            var index = $(tabSelector).index(this);
            $(tabSelector).removeClass(opt.tabActiveClass).eq(index).addClass(opt.tabActiveClass);
            $(bodySelector).removeClass(opt.bodyActiveClass).eq(index).addClass(opt.bodyActiveClass);
            $("html,body").animate({scrollTop: $(bodySelector).eq(index).offset().top - opt.scrollOffset}, 300);
            return false;
        });
        $(opt.scroller).on('scroll', function () {
            var top = $(opt.scroller).scrollTop();
            var index = 0;
            $(bodySelector).each(function (i, o) {
                var t = $(o).offset().top;
                if (top > t && top < t + $(o).height()) {
                    index = i;
                }
            });
            if (top + $(opt.scroller).height() === $(document).height()) {
                index = $(bodySelector).length - 1;
            }
            $(tabSelector).removeClass(opt.tabActiveClass).eq(index).addClass(opt.tabActiveClass);
            $(bodySelector).removeClass(opt.bodyActiveClass).eq(index).addClass(opt.bodyActiveClass);
        });
    }
}
