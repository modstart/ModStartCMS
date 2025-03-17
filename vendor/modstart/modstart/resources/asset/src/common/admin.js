window._rootWindow = MS.util.getRootWindow();
window._pageTabManager = {
    closeFromTab: function () {
        if (window == parent.window) {
            return
        }
        var tabPageId = window.frameElement.getAttribute("data-tab-page")
        window.parent._pageTabManager.close(tabPageId)
    },
    updateTitleFromTab: function (title) {
        if (!title) {
            return
        }
        if (window == parent.window) {
            return
        }
        var tabPageId = window.frameElement.getAttribute("data-tab-page")
        window.parent._pageTabManager.updateTitle(tabPageId, title)
    },
    activeUrl: function (url) {
        var $menu = _rootWindow.$('.ub-panel-frame .left .menu');
        var normalUrl = _rootWindow._pageTabManager.normalTabUrl(url);
        $menu.find('a').each(function (i, o) {
            var url = $(o).attr('href');
            if (url === 'javascript:;') {
                return;
            }
            if (_rootWindow._pageTabManager.normalTabUrl(url) === normalUrl) {
                //console.log('activeUrl', url)
                $(o).parents('.children').prev().addClass('open');
                $menu.find('.menu-item').removeClass('active');
                $(o).closest('.menu-item').addClass('active');
                $(o).parents('.menu-item').addClass('active');
                try {
                    o.scrollIntoView({block: 'center', behavior: 'smooth'});
                } catch (e) {
                }
            }
        });
    },
    blurUrl: function (url) {
        var $menu = _rootWindow.$('.ub-panel-frame .left .menu');
        var normalUrl = _rootWindow._pageTabManager.normalTabUrl(url);
        $menu.find('a').each(function (i, o) {
            var url = $(o).attr('href');
            if (url === 'javascript:;') {
                return;
            }
            if (_rootWindow._pageTabManager.normalTabUrl(url) === normalUrl) {
                $(o).parents('.menu-item').removeClass('active');
            }
        });
    }
};

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

    // 左侧菜单收起或展开
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

    // 后台Logo
    var $logo = $frame.find('.left .logo');
    $logo.on('click', function () {
        $($menu.find('a')[0]).click();
        return false;
    });

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
        $menu.find('[data-keywords-filter=show][data-menu-title]').each(function (i, o) {
            var $next = $(o).next();
            $next.attr('data-keywords-item', 'show');
            $next.find('[data-keywords-filter]').attr('data-keywords-filter', 'show').attr('data-keywords-item', 'show');
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


    // 左侧菜单拖拽
    // 让$adminTabMenu可以水平滚动
    // console.log('page-tabs-enable')
    var menuDragData = {
        draging: false,
        scrollTopStart: 0,
        startX: 0,
        startY: 0,
        isDragged: false,
    };
    $menu.on('mousedown', function (e) {
        menuDragData.draging = true;
        menuDragData.scrollTopStart = $menu.scrollTop();
        menuDragData.startX = e.pageX;
        menuDragData.startY = e.pageY;
        menuDragData.isDragged = false;
    });
    $menu.on('mousemove', function (e) {
        if (!menuDragData.draging) {
            return;
        }
        var offsetX = e.pageX - menuDragData.startX;
        var offsetY = e.pageY - menuDragData.startY;
        //console.log('mousemove', menuDragData.scrollTopStart, offsetX, offsetY, menuDragData.scrollTopStart - offsetY)
        if (offsetX * offsetX + offsetY * offsetY > 10) {
            menuDragData.isDragged = true;
        }
        $menu.scrollTop(menuDragData.scrollTopStart - offsetY);
        $menu.addClass('moving');
    })
    $menu.on('mouseup', function (e) {
        //console.log('mouseup', e.pageX, e.pageY)
        menuDragData.draging = false;
        $menu.removeClass('moving');
    });
    $menu.on('mouseleave', function (e) {
        //console.log('mouseleave', e.pageX, e.pageY)
        menuDragData.draging = false;
        $menu.removeClass('moving');
    });

    // 弹窗控制
    var $adminTabPage = $frame.find('#adminTabPage');
    var $adminTabMenu = $frame.find('#adminTabMenu');
    var $adminMainPage = $frame.find('#adminMainPage');
    var $adminTabRefresh = $frame.find('#adminTabRefresh');
    if ($('html').is('[page-tabs-enable]') && !isMobile) {
        // 让$adminTabMenu可以水平滚动
        // console.log('page-tabs-enable')
        var dragData = {
            draging: false,
            scrollLeftStart: 0,
            startX: 0,
            startY: 0,
            isDragged: false,
        };
        $adminTabMenu.on('mousedown', function (e) {
            dragData.draging = true;
            dragData.scrollLeftStart = $adminTabMenu.scrollLeft();
            dragData.startX = e.pageX;
            dragData.startY = e.pageY;
            dragData.isDragged = false;
        });
        $adminTabMenu.on('mousemove', function (e) {
            if (!dragData.draging) {
                return;
            }
            var offsetX = e.pageX - dragData.startX;
            var offsetY = e.pageY - dragData.startY;
            if (offsetX * offsetX + offsetY * offsetY > 10) {
                dragData.isDragged = true;
            }
            $adminTabMenu.scrollLeft(dragData.scrollLeftStart - offsetX);
            $adminTabMenu.addClass('moving');
        })
        $adminTabMenu.on('mouseup', function (e) {
            dragData.draging = false;
            $adminTabMenu.removeClass('moving');
        });
        $adminTabMenu.on('mouseleave', function (e) {
            dragData.draging = false;
            $adminTabMenu.removeClass('moving');
        });

        var tabManager = Object.assign(window._pageTabManager, {
            data: [],
            id: 1,
            runsOnFocus: [],
            normalTabUrl(url) {
                if (url.indexOf('_is_tab=1') > 0) {
                    return url
                }
                const u = new URL(url, document.baseURI)
                url = u.href
                let pcs = url.split('#');
                let path = pcs[0];
                path = path.replace(/\/$/, '')
                path = path + (path.indexOf('?') > -1 ? '&' : '?') + '_is_tab=1'
                return path + (pcs[1] ? '#' + pcs[1] : '');
            },
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
                url = this.normalTabUrl(url)
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
                    } else {
                        this.active(0);
                    }
                }
                this.data.splice(index, 1);
                this.updateMainPage()
            },
            updateMainPage: function () {
                let hasTab = (this.data.filter(o => o.active).length > 0)
                $adminMainPage.toggleClass('hidden', hasTab);
                $adminTabPage.toggleClass('hidden', !hasTab);
                let $menuMain = $menu.find('.menu-item.page-main').last();
                //console.log('updateMainPage', hasTab)
                $menuMain.toggleClass('active', !hasTab);
                if (!hasTab) {
                    //console.log('updateMainPage2', hasTab)
                    window.document.title = $.trim($menuMain.text());
                    $menuMain.parents('.menu-item').addClass('active');
                }
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
                    let $tabMenu = $adminTabMenu.find('[data-tab-menu-main]').addClass('active');
                    try {
                        $tabMenu[0].scrollIntoView({block: 'center', behavior: 'smooth'});
                    } catch (e) {
                    }
                    for (var i = 0; i < this.data.length; i++) {
                        if (this.data[i].active) {
                            this.data[i].option.blur()
                        }
                        this.data[i].active = false
                    }
                    this.updateMainPage()
                    return
                }
                $adminTabPage.find('iframe').addClass('hidden').filter('[data-tab-page=' + id + ']').removeClass('hidden');
                $adminTabMenu.find('a').removeClass('active').filter('[data-tab-menu=' + id + ']').addClass('active');
                let $tabMenu = $adminTabMenu.find('[data-tab-menu=' + id + ']');
                try {
                    $tabMenu[0].scrollIntoView({block: 'center', behavior: 'smooth'});
                } catch (e) {
                }
                for (var i = 0; i < this.data.length; i++) {
                    var active = (this.data[i].id == id)
                    if (this.data[i].active !== active) {
                        if (!active) {
                            this.data[i].option.blur()
                            this.data[i].active = active;
                        }
                    }
                }
                for (var i = 0; i < this.data.length; i++) {
                    var active = (this.data[i].id == id)
                    if (this.data[i].active !== active) {
                        if (active) {
                            this.data[i].option.focus()
                            this.data[i].active = active;
                        }
                    }
                }

                var $iframes = $adminTabPage.find('iframe:not(.hidden)');
                for (var i = 0; i < $iframes.length; i++) {
                    var $iframe = $iframes[i];
                    if ($iframe.contentWindow
                        && $iframe.contentWindow._pageTabManager
                        && $iframe.contentWindow._pageTabManager.runsOnFocus) {
                        for (var j = 0; j < $iframe.contentWindow._pageTabManager.runsOnFocus.length; j++) {
                            var fn = $iframe.contentWindow._pageTabManager.runsOnFocus[j];
                            fn();
                        }
                        $iframe.contentWindow._pageTabManager.runsOnFocus = [];
                    }
                }

                this.updateMainPage()


            },
            open: function (url, title, option) {
                option = Object.assign({
                    focus: function () {
                    },
                    blur: function () {
                    },
                }, option)
                url = this.normalTabUrl(url)
                var urlMain = this.normalTabUrl(window.location.href);
                if (urlMain === url) {
                    this.active(0)
                    return
                }
                var current = this.getByUrl(url);
                if (current) {
                    this.active(current.id);
                    return
                }
                $adminTabPage.append(`<iframe src="${url}" class="hidden" frameborder="0" data-tab-page="${this.id}"></iframe>`)
                $adminTabMenu.append(`<a href="javascript:;" data-tab-menu="${this.id}" draggable="false">${title}<i class="close iconfont icon-close"></i></a>`)
                this.data.push({
                    url: url,
                    title: title,
                    id: this.id,
                    active: false,
                    option: option,
                })
                this.active(this.id);
                this.id++;
            },
            updateTitle: function (id, title) {
                $adminTabMenu.find('[data-tab-menu=' + id + ']').html(title + '<i class="close iconfont icon-close"></i>')
                window.document.title = title
            },
        });
        $menu.find('a').on('click', function () {
            //console.log('click', this);
            var $this = $(this);
            if ($this.is('[data-tab-menu-ignore]')) {
                return;
            }
            let url = $this.attr('href');
            if (['javascript:;'].includes(url)) {
                return;
            }
            let title = $.trim($this.text())
            tabManager.open(url, title, {
                focus: function () {
                    //console.log('focus', url, $this.parents('.menu-item'))
                    $this.closest('.ub-panel-frame').find('.menu-item').removeClass('active')
                    $this.parent().addClass('active')
                    $this.parents('.menu-item').addClass('active');
                    window.document.title = $.trim($this.text())
                },
                blur: function () {
                    //console.log('blur', url, $this.parent()[0])
                    $this.parents('.menu-item').removeClass('active')
                }
            })
            return false;
        });
        $adminTabMenu.on('click', '[data-tab-menu-main]', function () {
            if (dragData.isDragged) {
                return;
            }
            tabManager.active(null)
            return false;
        });
        $adminTabMenu.on('click', '[data-tab-menu]', function () {
            if (dragData.isDragged) {
                return;
            }
            tabManager.active($(this).attr('data-tab-menu'))
        });
        $adminTabMenu.on('click', '[data-tab-menu] i.close', function () {
            tabManager.close($(this).parent().attr('data-tab-menu'));
            return false;
        });
        $adminTabRefresh.on('click', function () {
            tabManager.refresh();
            return false;
        });
        $(document).on('click', '[data-tab-open]', function () {
            let url = $(this).attr('href');
            if (['javascript:;'].includes(url)) {
                return;
            }
            let title = $(this).attr('data-tab-title');
            if (!title) {
                title = $(this).text();
            }
            _rootWindow._pageTabManager.open(url, title, {
                focus: function () {
                    tabManager.activeUrl(url);
                },
                blur: function () {
                    tabManager.blurUrl(url);
                }
            });
            return false;
        });
        window._pageTabManager = tabManager
    } else {
        $adminTabRefresh.remove();
    }

    // 全屏
    var $fullscreen = $frame.find('#fullScreenTrigger')
    $fullscreen.on('click', function () {
        MS.util.fullscreen.trigger();
        return false;
    });

    // 事件
    window.addEventListener('message', function (e) {
        const data = e.data;
        if (!data.type) {
            return
        }
        if (data.type !== 'FeedbackTicket:log' && data.type !== 'FeedbackTicket:env') {
            return
        }
        const sendMsg = function (type, data) {
            const iframe = $('iframe')[0]
            iframe.contentWindow.postMessage({type: type, data: data}, '*');
        }
        MS.api.post(window.__msAdminRoot + 'collect', {data: JSON.stringify(data)}, function (res) {
            MS.api.defaultCallback(res, {
                success: function (res) {
                    sendMsg(data.type, res.data)
                }
            })
        });
    });

});
