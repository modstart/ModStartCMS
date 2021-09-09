var BasicWebSocket = function () {
  var me = this;
  this._url = null;
  this._ws = null;
  this._debug = false
  this._callback = {
    open: null,
    message: null,
    close: null,
  }

  this.url = (url) => {
    me._url = url
    return me
  }
  this.debug = (debug) => {
    me._debug = debug
    return me
  }
  this.on = (event, callback) => {
    if (event in me._callback) {
      me._callback[event] = callback
      me.log('BindEvent -> ' + event)
    }
    return me
  }
  this.send = (data) => {
    me.log('Send -> ' + data)
    me._ws.send(data)
    return me
  }
  this.close = () => {
    me.log('Close')
    me._ws.close()
    return me
  }
  this.connect = () => {
    if (me._ws) {
      me.log('Close Old')
      me._ws.close()
    }
    me._ws = new window.WebSocket(me._url)
    me._ws.onopen = function () {
      me.log('OnOpen')
      if (me._callback.open) {
        me._callback.open(me)
      }
    };
    me._ws.onmessage = function (evt) {
      var msg = evt.data;
      me.log('OnMessage -> ' + JSON.stringify(msg))
      if (me._callback.message) {
        me._callback.message(me, msg)
      }
    };
    me._ws.onclose = function () {
      me.log('OnClose')
      if (me._callback.close) {
        me._callback.close(me)
      }
    }
    me.log('Connect -> ' + me._url)
    return me
  }


  this.ws = () => {
    return me._ws
  }
  this.log = (msg) => {
    if (me._debug) {
      console.log('WebSocket --> ' + msg)
    }
  }


  return me;
}
export {
  BasicWebSocket
}
