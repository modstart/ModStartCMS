const Ui = {
    /**
     * @Util 用于监听元素大小变化
     * @method MS.ui.onResize
     * @param ele Element 监听的元素
     * @param cb Function 回调函数
     */
    onResize(ele, cb) {
        if (!ele || !window.ResizeObserver) {
            return;
        }
        var doc = ele.ownerDocument;
        var win = doc.defaultView;
        var resizeTimer = null;
        // 如果不取 win.ResizeObserver 在父页面监听iframe的元素会抛异常
        // ResizeObserver loop completed with undelivered notifications
        var resizeObserver = new win.ResizeObserver(function (entries) {
            if (resizeTimer) {
                clearTimeout(resizeTimer);
            }
            resizeTimer = setTimeout(function () {
                cb();
            }, 1000);
        });
        resizeObserver.observe(ele);
    },
    state: {
        isSupport: function () {
            return window.history && window.history.pushState;
        },
        events: {
            change: [],
        },
        change: function (cb) {
            this.events.change.push(cb);
        },
        push: function (url, data) {
            data = data || {};
            data._url = url;
            window.history.pushState(data, null, url);
        },
        pushChange: function (url, data) {
            data = data || {};
            data._url = url;
            this.push(url, data);
            this.events.change.forEach(function (cb) {
                cb(data);
            });
        },
        init: function (initData) {
            initData = initData || {};
            initData._url = window.location.href;
            window.addEventListener('popstate', function (e) {
                MS.ui.state.events.change.forEach(function (cb) {
                    cb(e.state || initData);
                });
            });
        }
    },
    /**
     * @Util 获取页面大小
     * @method MS.ui.size
     * @return string sm,md,lg,xl
     */
    size: function () {
        // @width-sm-max: 40rem; // 0px    - 800px
        // @width-md: 40rem; // 800px - 1199px
        // @width-lg: 60rem; // 1200px - 1799px
        // @width-xl: 90rem; // 1800px -
        var w = window.innerWidth;
        if (w >= 1800) {
            return 'xl';
        } else if (w >= 1200) {
            return 'lg';
        } else if (w >= 800) {
            return 'md';
        }
        return 'sm';
    },
    /**
     * @Util 是否为指定大小的屏幕
     * @method MS.ui.isSize
     * @param sizes array ['sm','md','lg','xl']
     * @param cb function 回调函数
     */
    sizeCall: function (sizes, cb) {
        if (sizes === 'all') {
            sizes = ['sm', 'md', 'lg', 'xl'];
        }
        var size = MS.ui.size();
        if (sizes.indexOf(size) >= 0) {
            cb(size);
        }
    },
    sizeValue: function (defaultValue, sizeMap) {
        sizeMap = sizeMap || {};
        var size = MS.ui.size();
        if (size in sizeMap) {
            return sizeMap[size];
        }
        return defaultValue;
    }
}

Ui._htmlNav = {
    init: false,
    headings: []
};
Ui.htmlNav = function (htmlContainer, navContainer, option) {

    option = Object.assign({
        scrollToOffset: -80,
        positionOffset: 70,
        width: '12rem',
        open: function () {

        },
        close: function () {

        }
    }, option);

    var headings = [];
    var index = 0;
    var lavelMin = 100;
    $(htmlContainer).find('h1,h2,h3,h4,h5,h6').each(function () {
        var $this = $(this);
        var level = parseInt(this.tagName.replace('H', ''));
        lavelMin = Math.min(lavelMin, level);
        headings.push({
            index: index++,
            text: $.trim($this.text()),
            level: level,
            ele: this
        });
    });

    var tree = [];
    tree.push('<div class="ub-menu-tree-simple page tw-absolute tw-top-0 tw-right-0" style="width:' + option.width + ';">');
    tree.push('<div class="item-container">');
    for (var h of headings) {
        tree.push('<div class="item level-' + (h.level - lavelMin + 1) + '">');
        tree.push('<a href="javascript:;" data-index="' + h.index + '">' + MS.util.specialchars(h.text) + '</a>');
        tree.push('</div>');
    }
    tree.push('</div>');
    tree.push('<div class="tool">');
    tree.push('<a href="javascript:;" class="tool-close"><i class="iconfont icon-angle-up"></i></a>');
    tree.push('<a href="javascript:;" class="tool-open"><i class="iconfont icon-angle-down"></i></a>');
    tree.push('</div>');
    tree.push('</div>');

    $(navContainer).html(tree.join(''));

    Ui._htmlNav.headings = headings;

    if (!Ui._htmlNav.init) {
        Ui._htmlNav.init = true;
        $(document).on('click', '.ub-menu-tree-simple .item a', function () {
            var index = parseInt($(this).data('index'));
            MS.util.scrollTo(Ui._htmlNav.headings[index].ele, null, {
                offset: option.scrollToOffset
            });
        });
        $(document).on('click', '.ub-menu-tree-simple .tool .tool-close', function () {
            $(this).closest('.ub-menu-tree-simple').addClass('close');
            option.close && option.close();
        });
        $(document).on('click', '.ub-menu-tree-simple .tool .tool-open', function () {
            $(this).closest('.ub-menu-tree-simple').removeClass('close');
            option.open && option.open();
        });
        $(window).on('scroll', function () {
            var top = $(window).scrollTop();
            var activeIndex = -1;
            for (var l = Ui._htmlNav.headings.length - 1; l >= 0; l--) {
                if ($(Ui._htmlNav.headings[l].ele).offset().top > top) {
                    activeIndex = l;
                }
            }
            if (activeIndex >= 0) {
                $(navContainer).find('.item.active').removeClass('active');
                $(navContainer).find('.item').eq(activeIndex).addClass('active');
            }
        });
        $(window).on('scroll', function () {
            var top = Math.max($(window).scrollTop() - option.positionOffset, 0);
            $('.ub-menu-tree-simple').css('top', top + 'px');
        });
        $(window).trigger('scroll');
    }
};

module.exports = Ui;
