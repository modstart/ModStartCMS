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
    /**
     * @Util 默认回调函数
     * @method MS.api.defaultCallback
     * @param res string ajax返回的数据
     * @param callback object 回调函数
     * @example
     */
    defaultFormCallback: function (res, callback) {
        return Form.defaultCallback(res, callback, Dialog);
    },
    /**
     * @Util POST请求
     * @method MS.api.post
     * @param url string 请求地址
     * @param param object 请求参数
     * @param cb function 回调函数
     * @example
     * MS.api.post( '/login' , {username:'aa',password:'bb'}, function(res){
     *      // 请求完成
     *      MS.api.defaultCallback(res,{
     *          success:function(res){
     *              // 请求成功 (res.code===0）才会进入这里，
     *              // 如果请求失败自动按照默认规则处理（弹窗、跳转等）
     *          }
     *      })
     * })
     */
    post: function (url, param, cb) {
        $.ajax({
            type: 'post',
            url: url,
            dataType: "json",
            timeout: Form.defaultTimeout,
            data: param,
            success: function (res) {
                cb && cb(res);
            },
            error: function (res) {
                res = Form.responseToRes(res);
                cb && cb(res);
            }
        });
    },
    /**
     * @Util POST请求（成功）
     * @method MS.api.postSuccess
     * @param url string 请求地址
     * @param param object 请求参数
     * @param successCB function 成功回调函数
     * @param errorCB function 失败回调函数
     */
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
