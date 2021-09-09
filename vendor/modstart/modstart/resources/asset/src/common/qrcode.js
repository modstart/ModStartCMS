var JquryQrcode = require('./../lib/jqueryQrcode.js');

$(function () {
    $(document).on('click', '[data-page-qrcode]', function () {
        window.api.dialog.dialogContent('<div style="width:240px;height:240px;padding:20px;box-sizing:border-box;" data-qrcode-pop></div>', {
            openCallback: function () {
                $('[data-qrcode-pop]').qrcode({
                    size: 200,
                    text: window.location.href,
                    background: '#FFF',
                });
            }
        });
    });

    $(document).on('click', '[data-qrcode-content]', function () {
        var content = $(this).attr('data-qrcode-content');
        window.api.dialog.dialogContent('<div style="width:240px;height:240px;padding:20px;box-sizing:border-box;" data-qrcode-pop></div>', {
            openCallback: function () {
                $('[data-qrcode-pop]').qrcode({
                    size: 200,
                    text: content,
                    background: '#FFF',
                });
            }
        });
    });
})
