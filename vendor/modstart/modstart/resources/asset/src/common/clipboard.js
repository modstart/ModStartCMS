var Clipboard = require('./../vendor/clipboard/clipboard.js');

$(function () {
    var clipboard = new Clipboard('[data-clipboard-text]')
    clipboard.on('success', function (e) {
        window.api.dialog.tipSuccess('复制成功')
    })
})

window.api.clipboard = Clipboard