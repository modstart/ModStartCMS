var $ = require('jquery');
var Dialog = require('./dialog.js');
var Form = require('./form.js');
var Convenient = require('./convenient.js');

var initForm = function () {
    $(function () {
        $('form').each(function (i, o) {
            $(o).unbind('submit');
            var isAjaxForm = ($(o).attr('data-ajax-form') !== undefined);
            if (isAjaxForm) {
                Form.initAjax(o, Dialog);
            } else {
                if (!$(o).is('[data-form-no-loading]')) {
                    Form.initCommon(o, Dialog);
                }
            }
        });
    });
};
var initConvenient = function () {
    Convenient.init(Dialog);
};

var Base = {
    init: function () {
        initForm();
        initConvenient();
    },
    defaultFormCallback: function (res, callback) {
        return Form.defaultCallback(res, callback, Dialog);
    },
    post: function (url, param, cb) {
        $.ajax({
            type: 'post',
            url: url,
            dataType: "json",
            timeout: 10 * 60 * 1000,
            data: param,
            success: function (res) {
                cb && cb(res);
            },
            error: function () {
                cb && cb({
                    code: -999,
                    msg: "请求出现错误 T_T"
                });
            }
        });
    },
    postSuccess: function (url, param, successCB, errorCB) {
        successCB = successCB || Base.defaultFormCallback
        errorCB = errorCB || Base.defaultFormCallback
        Base.post(url, param, function (res) {
            Base.defaultFormCallback(res, {
                success: function (res) {
                    successCB(res)
                },
                error: function (res) {
                    errorCB(res)
                }
            })
        })
    }
};


module.exports = Base;
