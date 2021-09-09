import {Message, MessageBox, Loading} from 'element-ui'

let loading = null

export const Dialog = {
  loadingOn: function (msg) {
    loading = Loading.service({
      fullscreen: true,
      lock: true,
      text: msg || '加载中...',
      spinner: 'el-icon-loading',
      background: 'rgba(0,0,0, 0.4)'
    });
  },
  loadingOff: function () {
    loading.close()
  },
  tipSuccess: function (msg, cb) {
    var ms = 2000;
    if (msg && msg.length > 10) {
      ms = 1000 * parseInt(msg.length / 5);
    }
    Message({
      message: msg,
      type: 'success',
      duration: ms,
      onClose() {
        cb && cb()
      }
    })
  },
  tipError: function (msg, cb) {
    var ms = 2000;
    if (msg && msg.length > 10) {
      ms = 1000 * parseInt(msg.length / 5);
    }
    Message({
      message: msg,
      type: 'error',
      duration: ms,
      onClose() {
        cb && cb()
      }
    })
  },
  confirm: function (msg, cb, cancelCB) {
    MessageBox({
      title: '提示',
      message: msg,
      type: 'info',
      showCancelButton: true,
      callback: function (action) {
        if ('confirm' === action) {
          cb && cb()
        } else {
          cancelCB && cancelCB();
        }
      }
    })
  },
  alert: function (msg, cb) {
      MessageBox({
          title: '提示',
          message: msg,
          type: 'info',
          callback: function () {
              cb && cb()
          }
      })
  }
}
