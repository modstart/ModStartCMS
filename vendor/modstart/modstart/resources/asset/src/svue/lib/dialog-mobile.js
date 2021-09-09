import {MessageBox, Indicator, Toast} from 'mint-ui';

let loading = null

export const Dialog = {
    loadingOn: function (msg) {
        Indicator.open(msg || '加载中...')
    },
    loadingOff: function () {
        Indicator.close()
    },
    tipSuccess: function (msg, cb) {
        var ms = 2000;
        if (msg && msg.length > 10) {
            ms = 1000 * parseInt(msg.length / 5);
        }
        let instance = Toast({
            message: msg,
            iconClass: 'iconfont icon-checked toast-icon',
            duration: -1,
        })
        setTimeout(() => {
            instance.close()
            cb && cb()
        }, ms)
    },
    tipError: function (msg, cb) {
        var ms = 2000;
        if (msg && msg.length > 10) {
            ms = 1000 * parseInt(msg.length / 5);
        }
        let instance = Toast({
            message: msg,
            iconClass: 'iconfont icon-close-o toast-icon',
            duration: -1,
        })
        setTimeout(() => {
            instance.close()
            cb && cb()
        }, ms)
    },
    confirm: function (msg, cb, cancelCB) {
        MessageBox.confirm(msg).then(() => {
            cb && cb()
        }).catch(() => {
            cancelCB && cancelCB()
        })
    }
}
