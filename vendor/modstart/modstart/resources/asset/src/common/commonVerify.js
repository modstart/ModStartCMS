var $ = require('jquery');

var CommonVerify = function (option) {

    var opt = $.extend({
        generateServer: '',
        selectorTarget: '',
        selectorGenerate: '',
        selectorCountdown: '',
        selectorRegenerate: '',
        selectorCaptcha: '',
        selectorCaptchaImg: '',
        interval: 60,
        tipError: function (msg) {
            window.api.dialog.tipError(msg);
        },
        sendError: function (msg) {
            window.api.dialog.tipError(msg);
        },
        formData: function () {
            return {}
        }
    }, option);

    var countdown = 0;
    var setCountdown = function () {
        var $countdown = $(opt.selectorCountdown);
        if ($countdown.is('input')) {
            $countdown.val(countdown + ' s');
        } else {
            $countdown.html(countdown + ' s');
        }
        if (countdown > 0) {
            countdown--;
            setTimeout(setCountdown, 1000);
            $(opt.selectorCountdown).show();
            $(opt.selectorRegenerate).hide();
        } else {
            $countdown.hide();
            $(opt.selectorCountdown).hide();
            $(opt.selectorRegenerate).show();
        }
    };

    var sending = false;
    var checkAndGenerate = function () {
        var target = $(opt.selectorTarget).val();
        var captcha = null;
        if (opt.selectorCaptcha) {
            captcha = $(opt.selectorCaptcha).val();
            if (!captcha) {
                opt.tipError('图片验证码为空');
                return false;
            }
        }
        if (sending) {
            return false;
        }
        sending = true;
        window.api.dialog.loadingOn();
        var formData = opt.formData();
        window.api.base.post(opt.generateServer, Object.assign(formData, {
            target: target,
            captcha: captcha
        }), function (res) {
            window.api.dialog.loadingOff();
            sending = false;
            window.api.base.defaultFormCallback(res, {
                success: function (res) {
                    if (res.data) {
                        alert(res.data);
                    }
                    $(opt.selectorGenerate).hide();
                    countdown = opt.interval;
                    setCountdown(countdown);
                },
                error: function (res) {
                    $(opt.selectorCaptchaImg).click();
                    if (res.data) {
                        alert(res.data);
                    }
                    opt.sendError(res.msg);
                }
            });
        });
        return false;
    };

    $(opt.selectorGenerate).on('click', checkAndGenerate);
    $(opt.selectorRegenerate).on('click', checkAndGenerate);

};

window.api.commonVerify = CommonVerify;
