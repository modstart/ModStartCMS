window.__memberCheckCaptcha = function (param) {
    param = param || {captcha: $('[name=captcha]').val()};
    $('[data-captcha-status]').hide().filter('[data-captcha-status=loading]').show()
    window.api.base.post(window.__msRoot + 'register/captcha_verify', param, function (res) {
        window.api.base.defaultFormCallback(res, {
            success: function (res) {
                $('[data-captcha-status]').hide().filter('[data-captcha-status=success]').show();
                $('[name=captcha]').attr('data-form-process','success');
            },
            error: function (res) {
                $('[data-captcha-status]').hide().filter('[data-captcha-status=error]').show();
                $('[data-captcha]').click();
                $('[name=captcha]').attr('data-form-process','success');
            }
        })
    })
};
