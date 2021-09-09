import {Api} from "./api";
import {JsonUtil} from "./util";

export const UrlWatcher = function (option) {
    var opt = JsonUtil.extend({
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
    };

    this.stop = function () {
        if (me.running) {
            return;
        }
        me.running = false;
    };

    this.next = function () {
        setTimeout(me.sendRequest, opt.intervalInMS);
    };

    this.sendRequest = function () {
        if (!me.running) {
            return;
        }
        opt.preRequest();
        Api.postJson(opt.url, opt.data, function (res) {
            opt.requestFinish(res);
        });
    };

};

