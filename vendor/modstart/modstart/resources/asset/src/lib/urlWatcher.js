var UrlWatcher = function (option) {

    var opt = $.extend({
        intervalInMS: 3000,
        data: null,
        url: null,
        maxRound: 100,
        jsonp: false,
        preRequest: function () {
        },
        requestFinish: function (res) {

        },
        expired: function () {

        }
    }, option);

    var me = this;

    this.running = false;
    this.currentRound = 0

    this.start = function () {
        me.running = true;
        me.currentRound = 0;
        setTimeout(me.sendRequest, opt.intervalInMS);
        return me
    };

    this.stop = function () {
        if (!me.running) {
            return;
        }
        me.running = false;
        return me
    };

    this.next = function () {
        setTimeout(me.sendRequest, opt.intervalInMS);
        return me
    };

    this.sendRequest = function () {
        if (!me.running) {
            return;
        }
        me.currentRound++
        if (me.currentRound > opt.maxRound) {
            me.running = false;
            opt.expired();
            return;
        }
        opt.preRequest();
        if (opt.jsonp) {
            $.ajax({
                url: opt.url,
                dataType: 'jsonp',
                timeout: 10 * 1000,
                data: opt.data,
                success: (res) => {
                    opt.requestFinish.call(me, res);
                },
                error: (res) => {
                    opt.requestFinish.call(me, {code: -1, msg: '请求出现错误'});
                },
                jsonp: 'callback',
            });
        } else {
            $.post(opt.url, opt.data, function (res) {
                opt.requestFinish.call(me, res);
            });
        }
    };

};

module.exports = UrlWatcher;
