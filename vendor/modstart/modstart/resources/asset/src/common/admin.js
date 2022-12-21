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
    var $keywords = $frame.find('#menuSearchKeywords');
    $keywords.on('keyup', function () {
        var keywords = $(this).val();
        if (menuFilterTimer) {
            clearTimeout(menuFilterTimer);
        }
        menuFilterTimer = setTimeout(function () {
            filterMenu(keywords);
        }, 200);
    });
    if ($keywords.val()) {
        $keywords.trigger('keyup');
    }

    // 弹窗控制
    var $adminTabPage = $frame.find('#adminTabPage');
    var $adminTabMenu = $frame.find('#adminTabMenu');
    var $adminMainPage = $frame.find('#adminMainPage');
    var $adminTabRefresh = $frame.find('#adminTabRefresh');
    var tabManager = {
        data: [],
        id: 1,
        getIndexById: function (id) {
            id = parseInt(id)
            for (var i = 0; i < this.data.length; i++) {
                if (this.data[i].id == id) {
                    return i;
                }
            }
            return null;
        },
        getById: function (id) {
            var index = this.getIndexById(id);
            return (null === index) ? null : this.data[index];
        },
        getByUrl: function (url) {
            for (var i = 0; i < this.data.length; i++) {
                if (this.data[i].url == url) {
                    return this.data[i];
                }
            }
            return null;
        },
        close: function (id) {
            var index = this.getIndexById(id);
            if (null === index) {
                return;
            }
            var tab = this.data[index];
            $adminTabPage.find('[data-tab-page=' + id + ']').remove();
            $adminTabMenu.find('[data-tab-menu=' + id + ']').remove();
            if (tab.active) {
                if (index > 0) {
                    this.active(this.data[index - 1].id);
                } else if (index < this.data.length - 1) {
                    this.active(this.data[index + 1].id);
                }
            }
            this.data.splice(index, 1);
            this.updateMainPage()
        },
        updateMainPage: function () {
            let hasTab = (this.data.filter(o => o.active).length > 0)
            $adminMainPage.toggleClass('hidden', hasTab);
            $adminTabPage.toggleClass('hidden', !hasTab);
        },
        activeId: function () {
            for (var i = 0; i < this.data.length; i++) {
                if (this.data[i].active) {
                    return this.data[i].id;
                }
            }
            return null
        },
        activeByUrl: function (url) {
            let tab = this.getByUrl(url);
            if (!tab) {
                return
            }
            this.active(tab.id)
        },
        refresh: function () {
            let activeId = this.activeId()
            if (!activeId) {
                window.location.reload()
                return
            }
            let $iframe = $adminTabPage.find('iframe[data-tab-page=' + activeId + ']')
            $iframe[0].contentWindow.location.reload()
        },
        active: function (id) {
            if (!id) {
                $adminTabPage.find('iframe').addClass('hidden')
                $adminTabMenu.find('a').removeClass('active')
                try {
                    let $menu = $adminTabMenu.find('[data-tab-menu-main]').addClass('active');
                    $menu[0].scrollIntoView({block: 'center', behavior: 'smooth'});
                } catch (e) {
                }
                for (var i = 0; i < this.data.length; i++) {
                    this.data[i].active = false
                }
                this.updateMainPage()
                return
            }
            $adminTabPage.find('iframe').addClass('hidden').filter('[data-tab-page=' + id + ']').removeClass('hidden');
            $adminTabMenu.find('a').removeClass('active').filter('[data-tab-menu=' + id + ']').addClass('active');
            try {
                let $menu = $adminTabMenu.find('[data-tab-menu=' + id + ']');
                $menu[0].scrollIntoView({block: 'center', behavior: 'smooth'});
            } catch (e) {
            }
            for (var i = 0; i < this.data.length; i++) {
                this.data[i].active = (this.data[i].id == id);
            }
            this.updateMainPage()
        },
        open: function (url, title) {
            var current = this.getByUrl(url);
            if (current) {
                this.active(current.id);
                return
            }
            let tabUrl = url + (url.indexOf('?') > -1 ? '&' : '?') + '_is_tab=1'
            $adminTabPage.append(`<iframe src="${tabUrl}" class="hidden" frameborder="0" data-tab-page="${this.id}"></iframe>`)
            $adminTabMenu.append(`<a href="javascript:;" data-tab-menu="${this.id}">${title}<i class="close iconfont icon-close"></i></a>`)
            this.data.push({
                url: url,
                title: title,
                id: this.id,
                active: false,
            })
            this.active(this.id);
            this.id++;
        }
    };
    if (!isMobile) {
        $menu.find('a').on('click', function () {
            let url = $(this).attr('href');
            if (['javascript:;'].includes(url)) {
                return;
            }
            let title = $(this).text()
            tabManager.open(url, title)
            return false;
        });
        $adminTabMenu.on('click', '[data-tab-menu-main]', function () {
            tabManager.active(null)
            return false;
        });
        $adminTabMenu.on('click', '[data-tab-menu]', function () {
            tabManager.active($(this).attr('data-tab-menu'))
            return false;
        });
        $adminTabMenu.on('click', '[data-tab-menu] i.close', function () {
            tabManager.close($(this).parent().attr('data-tab-menu'));
            return false;
        });
        $adminTabRefresh.on('click', function () {
            tabManager.refresh();
            return false;
        });
    } else {
        $adminTabRefresh.remove();
    }

    // 全屏
    var $fullscreen = $frame.find('#fullScreenTrigger')
    $fullscreen.on('click', function () {
        MS.util.fullscreen.trigger();
        return false;
    });

});
