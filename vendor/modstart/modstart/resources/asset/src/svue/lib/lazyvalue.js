const LazyValue = function () {
    var me = this;
    me._url = null;
    me._interval = 3000;
    me._update = null;
    me._updateOne = null;
    me._finish = null;
    me._fetch = null;
    me.url = function (url) {
        me._url = url
        return me
    }
    me.interval = function (ms) {
        me._interval = ms
        return me
    }
    me.update = function (cb) {
        me._update = cb;
        return me;
    }
    me.updateOne = function (cb) {
        me._updateOne = cb;
        return me;
    }
    me.finish = function (cb) {
        me._finish = cb;
        return me;
    }
    me.fetch = function (cb) {
        me._fetch = cb;
        return me;
    }
    me.start = function () {
        if (!me._fetch) {
            if (MS && MS.api && MS.api.post) {
                me._fetch = function (url, cb) {
                    MS.api.post(url, {}, cb);
                }
            }
        }
        var watch = function () {
            me._fetch(me._url, function (res) {
                if (res.code == 0) {
                    me._update && me._update(res.data.value)
                    if (me._updateOne) {
                        for (var k in res.data.value) {
                            me._updateOne(k, res.data.value[k])
                        }
                    }
                    if (res.data.status == 'finish') {
                        me._finish && me._finish(res.data.value)
                    } else {
                        setTimeout(function () {
                            watch()
                        }, me._interval);
                    }
                } else {
                    alert('请求出现错误：' + JSON.stringify(res));
                }
            });
        };
        watch();
        return me
    }
    return me;
};


export {
    LazyValue
}
