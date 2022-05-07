/**
 * 无限下拉显示插件
 *
 * @example
 *

 var $lister = $('.lister');
 var lister = new InfiniteScroller({
    container: window,
    tail: $lister.find('.ub-loading'),
    autoStart: false,
    initCallback: () => {
    },
    nextCallback: (page) => {
        this.$api.post('?', {page, pageSize}, res => {
            //TODO process res.data.records
            if (res.data.pageSize === res.data.records.length) {
                lister.nextDone();
            } else {
                lister.done();
            }
        }, res => {

        });
    },
    doneCallback: () => {
        $lister.find('.ub-loading').hide();
        $lister.find('.ub-no-more').show();
    },
    resetCallback: () => {
        $lister.find('.list').html('');
        $lister.find('.ub-loading').show();
        $lister.find('.ub-no-more').hide();
    }
});

 lister.setPage(1);
 lister.start();


 */

;(function () {

    var InfiniteScroller = function (option) {

        if (typeof $ == "undefined") {
            alert("InfiniteScroller require jQuery");
            return;
        }

        var defaultOption = {
            container: null,
            tail: null,
            autoStart: true,
            initCallback: function () {
            },
            nextCallback: function (page) {
            },
            doneCallback: function () {
            },
            resetCallback: function () {
            },
            pullToRefreshCallback: function () {
            },
            pullToRefresh: false,
            pullToRefreshDom: null
        };

        this.opt = $.extend(defaultOption, option);

        if (null === this.opt.container) {
            this.opt.container = window;
        }

        this.dom = {
            container: null,
            tail: null
        };

        this.runtime = {
            processNext: false,
            done: false,
            page: 1,
            started: false,
        };

        var me = this;
        this.dom.container = $(this.opt.container);
        this.dom.tail = $(this.opt.tail);
        this.dom.pullToRefresh = null;
        this.dom.container.on('scroll', function () {
            me._nextCheck();
        });

        this.opt.initCallback();

        if (this.opt.autoStart) {
            this.runtime.started = true;
            me._nextCheck();
        }

        if (this.opt.pullToRefresh) {
            me.dom.pullToRefresh = $(this.opt.pullToRefreshDom);
            var pullToRefreshHeight = me.dom.pullToRefresh.height();
            var direction = null;
            var startTouch = {
                x: 0,
                y: 0,
            };
            var marginTop = 99999;
            var maxScrollHeight = 0;
            me.dom.container.on('touchstart', function (e) {
                e = e.originalEvent;
                maxScrollHeight = 0;
                marginTop = 99999;
                if (me.dom.container.scrollTop() <= 0) {
                    direction = 'up';
                }
                if (direction == 'up') {
                    startTouch.x = e.changedTouches[0].pageX;
                    startTouch.y = e.changedTouches[0].pageY;
                    me.dom.pullToRefresh.find('.status').hide();
                    me.dom.pullToRefresh.find('.status.pull').show();
                }
            });
            me.dom.container.on('touchmove', function (e) {
                e = e.originalEvent;
                e.preventDefault();
                if (direction == 'up') {
                    // 横向滑屏阻止 避免横向滑屏影响
                    var dDis = Math.abs(e.touches[0].pageX - startTouch.x) - Math.abs(e.touches[0].pageY - startTouch.y);
                    if (dDis > 0) {
                        return false;
                    }
                    var dis = e.changedTouches[0].pageY - startTouch.y;
                    if (dis > maxScrollHeight) {
                        // 向下滑
                        maxScrollHeight = Math.max(maxScrollHeight, dis);
                        if (dis > pullToRefreshHeight) {
                            dis = pullToRefreshHeight;
                        }
                        marginTop = dis - pullToRefreshHeight;
                    } else {
                        // 向上滑
                        marginTop = marginTop - (maxScrollHeight - dis);
                        maxScrollHeight = dis;
                        // console.log(height);
                        if (marginTop < -pullToRefreshHeight) {
                            marginTop = -pullToRefreshHeight;
                        }
                    }
                    if (dis > 0) {
                        me.dom.pullToRefresh.css({marginTop: marginTop + 'px'});
                    }
                }
            });
            me.dom.container.on('touchend', function (e) {
                e = e.originalEvent;
                if (direction == 'up') {
                    direction = null;
                    if (marginTop == 0) {
                        me.dom.pullToRefresh.find('.status').hide();
                        me.dom.pullToRefresh.find('.status.refreshing').show();
                        me.runtime.page = 1;
                        me.opt.pullToRefreshCallback(me.runtime.page);
                    } else {
                        me.dom.pullToRefresh.css({marginTop: (-pullToRefreshHeight) + 'px'});
                    }
                }
            });
        }

    };

    InfiniteScroller.prototype = {
        _isTailOnScreen: function () {

            if (!this.dom.tail.is(':visible')) {
                return false;
            }

            var win = $(window);
            var viewport = {
                top: win.scrollTop(),
                left: win.scrollLeft()
            };
            viewport.width = win.width();
            viewport.height = win.height()
            viewport.x = viewport.left + viewport.width / 2;
            viewport.y = viewport.top + viewport.height / 2;

            var bounds = this.dom.tail.offset();
            bounds.width = this.dom.tail.outerWidth();
            bounds.height = this.dom.tail.outerHeight();
            bounds.x = bounds.left + bounds.width / 2;
            bounds.y = bounds.top + bounds.height / 2;

            // 元素DOM还没有准备好
            if (bounds.left == 0 && bounds.right == 0) {
                return false;
            }

            // 判断两个视窗是否相交
            var visible = true;
            if (visible && Math.abs(viewport.x - bounds.x) * 2 >= (viewport.width + bounds.width)) {
                visible = false;
            }

            if (visible && Math.abs(viewport.y - bounds.y) * 2 >= (viewport.height + bounds.height)) {
                visible = false;
            }

            // console.log('viewport->');
            // console.log(viewport);
            // console.log('bounds->');
            // console.log(bounds);
            // console.log('visible->');
            // console.log(visible);

            return visible;
        },
        _nextCheck: function () {
            if (this.runtime.started && !this.runtime.done && !this.runtime.processNext && this._isTailOnScreen()) {
                this._next();
                return true;
            }
            return false;
        },
        _next: function () {
            this.runtime.processNext = true;
            this.opt.nextCallback(this.runtime.page);
        },

        // 立即检查是否需要更新
        check: function () {
            this._nextCheck();
        },
        // 当一页完成需要调用这个
        nextDone: function () {
            this.runtime.processNext = false;
            this.runtime.page++;
            this._nextCheck();
        },
        // 没有记录需要调用这个方法
        done: function () {
            this.runtime.done = true;
            this.opt.doneCallback();
        },
        // 重置
        reset: function () {
            this.opt.resetCallback();
            this.runtime.processNext = false;
            this.runtime.done = false;
            this.runtime.page = 1;
            this._nextCheck();
        },
        // 开始
        start: function () {
            this.runtime.started = true;
            this._nextCheck();
        },
        // 设置页码
        setPage: function (page) {
            this.runtime.page = page;
        },
        // 下拉刷新完成
        pullToRefreshDone: function () {
            this.dom.pullToRefresh.css({marginTop: (-this.dom.pullToRefresh.height()) + 'px'});
            this.reset();
        }
    };

    if (typeof module !== 'undefined' && typeof exports === 'object' && define.cmd) {
        module.exports = InfiniteScroller;
    } else if (typeof define === 'function' && define.amd) {
        define(function () {
            return InfiniteScroller;
        });
    } else {
        this.InfiniteScroller = InfiniteScroller;
    }

}).call(function () {
    return this || (typeof window !== 'undefined' ? window : global);
}());

