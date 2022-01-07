$(window).on('load', function () {
    /**
     * 修复部分依赖窗口滚动时间的组件（ueditor顶部悬浮工具栏)
     */
    $('.ub-panel-frame > .right > .content.fixed, .ub-panel-dialog .panel-dialog-body').scroll(function () {
        var evt = document.createEvent('HTMLEvents');
        evt.initEvent('scroll', false, true);
        window.dispatchEvent(evt);
    });
    var isMobile = ($(window).width() < 600)
    var $frame = $('.ub-panel-frame');
    $frame.find('.menu-expand-all').on('click', function () {
        $frame.find('.left .menu .title').addClass('open');
    });
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

    // 后台菜单搜索
    var $menu = $frame.find('.left .menu');
    var markText = function (str, indexs) {
        return str.substring(0, indexs[0]) + '<mark>' + str.substring(indexs[0], indexs[1] + 1) + '</mark>' + str.substring(indexs[1] + 1)
    };
    var filterMenu = function (keywords) {
        keywords = $.trim(keywords);
        if (!keywords) {
            $menu.find('[data-keywords-filter]').attr('data-keywords-filter', 'show');
            $menu.find('[data-keywords-item]').attr('data-keywords-item', 'show');
            $menu.find('[data-keywords-filter]').each(function (i, o) {
                var text = $(o).text().trim();
                $(o).find('span').html(text);
            });
            return;
        }
        $menu.find('.title').addClass('open');
        $menu.find('[data-keywords-filter]').attr('data-keywords-filter', 'hide');
        $menu.find('[data-keywords-item]').attr('data-keywords-item', 'hide');
        $menu.find('[data-keywords-filter]').each(function (i, o) {
            var text = $(o).text().trim();
            var indexs = PinyinMatch.match(text, keywords);
            var colorText = text
            if (false !== indexs) {
                colorText = markText(text, indexs);
                $(o).attr('data-keywords-filter', 'show');
                $(o).attr('data-keywords-item', 'show');
            }
            $(o).find('span').html(colorText);
        });
        $menu.find('>.menu-item>.children>.menu-item>.children').each(function (i, o) {
            if ($(o).find('[data-keywords-filter=show]').length > 0) {
                $(o).attr('data-keywords-item', 'show').prev().attr('data-keywords-item', 'show');
            }
        });
        $menu.find('>.menu-item>.children').each(function (i, o) {
            if ($(o).find('[data-keywords-filter=show]').length > 0) {
                $(o).attr('data-keywords-item', 'show').prev().attr('data-keywords-item', 'show');
            }
        });
    };
    var menuFilterTimer = null;
    $frame.find('#menuSearchKeywords').on('keyup', function () {
        var keywords = $(this).val();
        if (menuFilterTimer) {
            clearTimeout(menuFilterTimer);
        }
        menuFilterTimer = setTimeout(function () {
            filterMenu(keywords);
        }, 200);
    });
});
