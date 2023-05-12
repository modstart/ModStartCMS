var UrlWatcher = function (option) {

    var opt = $.extend({
        intervalInMS: 3000,
        data: null,
        url: null,
        maxRound: 100,
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
        $.post(opt.url, opt.data, function (res) {
            opt.requestFinish.call(me, res);
        });
    };

};

module.exports = UrlWatcher;
