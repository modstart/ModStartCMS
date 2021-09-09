import stompjs from 'stompjs-websocket'

var BasicStomp = function () {
  var me = this;
  this._url = null;
  this._client = null;
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
  this.send = (destination, message) => {
    me.log('Send -> ' + message)
    me._client.send(destination, {}, message)
    return me
  }
  this.close = () => {
    me.log('Close')
    me._client.disconnect()
    return me
  }
  this.connect = () => {
    if (me._client) {
      me.log('Close Old')
      me._client.disconnect()
    }
    me._client = stompjs.client(me._url);
    me._client.heartbeat.outgoing = 5000;
    me._client.heartbeat.incoming = 0;
    me._client.connect({a: 1}, function (success) {
      me.log('OnOpen Success')
      if (me._callback.open) {
        me._callback.open(me)
      }
    }, function (error) {
      me.log('OnOpen Fail')
      console.error(error)
    });
    me.log('Connect -> ' + me._url)
    return me
  }


  this.client = () => {
    return me._client
  }
  this.log = (msg) => {
    if (me._debug) {
      console.log('Stomp --> ' + msg)
    }
  }

  return me;
}
export {
  BasicStomp
}
