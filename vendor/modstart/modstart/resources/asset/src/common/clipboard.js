var Clipboard = require('./../vendor/clipboard/clipboard.js');

$(function () {
    var clipboard = new Clipboard('[data-clipboard-text]')
    clipboard.on('success', function (e) {
        window.api.dialog.tipSuccess('复制成功')
    })
})

if (!('api' in window)) {
    window.api = {}
}
window.api.clipboard = Clipboard

if (!('MS' in window)) {
    window.MS = {}
}
window.MS.clipboard = Clipboard
window.MS.clip = {
    copyText: function (text, cb) {
        cb = cb || function () {
            MS.dialog.tipSuccess('复制成功')
        }
        var copyInput = document.createElement('textarea');
        copyInput.style.position = 'fixed';
        copyInput.style.top = '-1000px';
        copyInput.style.left = '-1000px';
        copyInput.value = text;
        document.body.appendChild(copyInput);
        copyInput.select();
        copyInput.setSelectionRange(0, 99999);
        document.execCommand("Copy");
        document.body.removeChild(copyInput);
        cb();
    },
    copyImage: function (imageBase64, format, cb) {
        cb = cb || function () {
            MS.dialog.tipSuccess('复制成功')
        }
        format = format || 'png';
        let mime = 'image/' + format;
        if ('svg' === format) {
            mime = 'image/svg+xml';
        }

        if (!window.ClipboardItem) {
            alert('暂不支持');
            return;
        }

        // navigator.permissions.query({ name: 'clipboard-write' }).then(result => {
        //     console.log('xxxx',result);
        //     if (result.state === 'granted') {
        //         const type = 'text/plain';
        //         const blob = new Blob([message], { type });
        //         let data = [new ClipboardItem({ [type]: blob })];
        //         navigator.clipboard.write(data).then(function() {
        //
        //         }, function() {
        //
        //         });
        //     }
        // })

        const img = new Image();
        img.onload = function () {
            const canvas = document.createElement('canvas');
            canvas.width = img.width;
            canvas.height = img.height;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0);
            canvas.toBlob(function (blob) {
                const item = new window.ClipboardItem({[mime]: blob});
                navigator.clipboard.write([item]).then(function () {
                    cb();
                }).catch(function (error) {
                    console.error('复制失败', error);
                });
            }, mime);
        };
        img.onerror = function () {
            console.error('Failed to load image:', img.src);
        };
        img.src = 'data:' + mime + ';base64,' + imageBase64;
        // console.log(format, img.src)
    }
}
