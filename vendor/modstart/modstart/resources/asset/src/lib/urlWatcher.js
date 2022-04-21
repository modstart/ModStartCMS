var UrlWatcher = function (option) {

    var opt = $.extend({
        intervalInMS: 2000,
        data: null,
        url: null,
        preRequest: function () {
        },
        requestFinish: function (res) {

        }
    }, option);

    var me = this;

    this.running = false;

    this.start = function () {
        me.running = true;
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
        opt.preRequest();
        $.post(opt.url, opt.data, function (res) {
            opt.requestFinish.call(me, res);
        });
    };

};

module.exports = UrlWatcher;
