/**
 *
 * @returns {ListCollector}
 * @constructor
 *
 * @example
 *
 var maxId = 0;
 new ListCollector().fetch(function (cb, me) {
    let res = {code: 0, msg: '', finished: false, list: [['aaa']]};
    maxId++;
    if (maxId > 10) {
      res.finished = true;
    }
    console.log('fetch', res);
    cb(res)
  }).finish(function (list, me) {
    console.log('finish', list)
  }).error(function (msg, me) {
    console.log('error', msg);
  }).start();
 */
var ListCollector = function () {
  var me = this
  this._list = [];
  this._fetch = null;
  this._finish = null;
  this._error = function (msg, me) {
    alert(msg);
  };
  this._interval = 1000;
  this.clear = function () {
    me._list = [];
    return me;
  };
  this.fetch = function (fetch) {
    me._fetch = fetch;
    return me;
  };
  this.finish = function (finish) {
    me._finish = finish;
    return me;
  };
  this.error = function (error) {
    me._error = error;
    return me;
  };
  this.interval = function (interval) {
    me._interval = interval;
    return me;
  };
  this.start = function () {
    var execute = function () {
      me._fetch(function (res) {
        if (res.code === 0) {
          res.list.forEach(o => {
            me._list.push(o);
          });
          if (res.finished) {
            me._finish(me._list, me);
          } else {
            setTimeout(function () {
              execute();
            }, me._interval);
          }
        } else {
          me._error(res.msg);
        }
      }, me);
    };
    execute();
    return me
  }
  return this
}

/**
 *
 * @returns {ListDispatcher}
 * @constructor
 * @example

 new ListDispatcher()
 .set([['a'], ['a'], ['a'], ['a'], ['a'], ['a'], ['a'], ['a']])
 .chunk(2)
 .error(function (msg, me) {
            alert(msg)
          })
 .dispatch(function (list, cb, me) {
            console.log('dispatch', list);
            cb({code: 0, msg: ''});
          })
 .finish(function (me) {
            console.log('finish');
          })
 .start();
 */
var ListDispatcher = function () {
  var me = this
  this._list = [];
  this._dispatch = null;
  this._finish = null;
  this._chunk = 1;
  this._error = function (msg, me) {
    alert(msg);
  };
  this._interval = 1000;
  this.set = function (list) {
    me._list = list;
    return me;
  };
  this.dispatch = function (dispatch) {
    me._dispatch = dispatch;
    return me;
  };
  this.finish = function (finish) {
    me._finish = finish;
    return me;
  };
  this.chunk = function (chunk) {
    me._chunk = chunk;
    return me;
  };
  this.error = function (error) {
    me._error = error;
    return me;
  };
  this.interval = function (interval) {
    me._interval = interval;
    return me;
  };
  this.start = function () {
    var execute = function () {
      var chunk = null;
      if (me._list.length === 0) {
        me._finish(me);
        return;
      }
      if (me._list.length > me._chunk) {
        chunk = me._list.splice(0, me._chunk);
      } else {
        chunk = me._list;
        me._list = [];
      }
      me._dispatch(chunk, function (res) {
        try {
          if (res.code === 0) {
            if (me._list.length === 0) {
              me._finish(me);
            } else {
              setTimeout(function () {
                execute();
              }, me._interval);
            }
          } else {
            me._error(res.msg);
          }
        } catch (e) {
          console.error('ListDispatcherError',e)
          me._error('上传出现错误')
        }
      }, me);
    };
    execute();
    return me
  }
  return this
}

export {
  ListCollector,
  ListDispatcher
}
