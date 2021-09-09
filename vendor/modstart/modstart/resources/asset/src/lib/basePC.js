var $ = require('jquery');
var DialogPC = require('./dialogPC.js');
var Form = require('./form.js');
var Convenient = require('./convenient.js');

var initForm = function () {
    $(function () {
        $('form').each(function (i, o) {
            $(o).unbind('submit');
            var isAjaxForm = ($(o).attr('data-ajax-form') !== undefined);
            if (isAjaxForm) {
                Form.initAjax(o, DialogPC);
            } else {
                if (!$(o).is('[data-form-no-loading]')) {
                    Form.initCommon(o, DialogPC);
                }
            }
        });
    });
};
var initConvenient = function () {
    Convenient.init(DialogPC);
};

var Base = {
    init: function () {
        initForm();
        initConvenient();
    },
    defaultFormCallback: function (res, callback) {
        return Form.defaultCallback(res, callback, DialogPC);
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