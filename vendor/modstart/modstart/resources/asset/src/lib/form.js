var $ = require('jquery');
var Util = require('./util.js');
var EventManager = require('./event-manager.js');

var Form = {
    defaultCallback: function (res, callback, Dialog) {

        Dialog = Dialog || null;

        callback = callback || {};

        if (typeof res !== 'object') {
            alert("ErrorResponse:" + res);
            return;
        }

        if (!("code" in res)) {
            Form.defaultCallback({code: -1, msg: 'parse error: ' + JSON.stringify(res)}, callback, Dialog);
            //alert("ErrorResponseObject:" + res.toString());
            return;
        }

        var code = res.code, msg = "", redirect = "", data = null;
        if ("msg" in res) {
            msg = res.msg;
        }
        if ("redirect" in res) {
            redirect = res.redirect;
        }
        if ("data" in res) {
            data = res.data;
        }

        var isWeiXin = function () {
            var ua = window.navigator.userAgent.toLowerCase();
            if (ua.match(/MicroMessenger/i) == 'micromessenger') {
                return true;
            } else {
                return false;
            }
        };

        // 解决安卓微信浏览器中location.reload 或者 location.href失效的问题
        var winReload = function (w) {
            if (isWeiXin()) {
                var l = w.location;
                var url = [];
                var t = '_t_' + (new Date().getTime()) + '_';
                url.push(l.protocol);
                url.push('//');
                url.push(l.host);
                url.push(l.pathname);
                if (l.search) {
                    if (/_t_\d+_/.test(l.search)) {
                        url.push(l.search.replace(/_t_\d+_/, t));
                    } else {
                        url.push(l.search);
                        url.push('&');
                        url.push(t);
                    }
                } else {
                    url.push(l.search);
                    url.push('?');
                    url.push(t);
                }
                url.push(l.hash);
                w.location.replace(url.join(''));
            } else {
                w.location.reload();
            }
        };

        var redirectFunc = function (redirect) {
            if (!redirect) {
                return;
            }
            if ('[reload]' === redirect) {
                winReload(window);
            } else if ('[root-reload]' == redirect) {
                winReload(Util.getRootWindow())
            } else if ('[back]' === redirect) {
                window.history.back();
            } else if (redirect.indexOf('[js]') === 0) {
                eval(redirect.substr(4));
            } else {
                window.location.href = redirect;
            }
        };

        var successFunc = function () {
            if ("success" in callback) {
                callback.success(res);
            } else if (redirect) {
                if (msg) {
                    if (Dialog) {
                        Dialog.alertSuccess(msg, function () {
                            redirectFunc(redirect);
                        });
                    } else {
                        alert(msg);
                        redirectFunc(redirect);
                    }
                } else {
                    redirectFunc(redirect);
                }
            } else {
                if (msg) {
                    if (Dialog) {
                        Dialog.tipSuccess(msg);
                    } else {
                        alert(msg);
                    }
                }
            }
        };

        var errorFunc = function () {
            if ("error" in callback) {
                callback.error(res);
            } else if (redirect) {
                if (msg) {
                    if (Dialog) {
                        Dialog.alertError(msg, function () {
                            redirectFunc(redirect);
                        });
                    } else {
                        alert(msg);
                        redirectFunc(redirect);
                    }
                } else {
                    redirectFunc(redirect);
                }
            } else {
                if (Dialog) {
                    Dialog.tipError(msg);
                } else {
                    alert(msg);
                }
            }
        };

        if (code == 0) {
            successFunc();
        } else {
            errorFunc();
        }

    },
    // ajax表单初始化
    initAjax: function (form, Dialog) {

        Dialog = Dialog || null;

        var $form = $(form);
        $form.on('submit', function () {

            // 防止重入
            if ($form.data('submiting')) {
                return false;
            }

            var action = $(this).attr("action");
            var method = $(this).attr("method");
            var callbackValidate = $(this).data('callbackValidate');
            var callback = $(this).data('callback');

            if (callbackValidate && !callbackValidate()) {
                return false;
            }

            if (!callback) {
                callback = Form.defaultCallback;
            }

            if (!method) {
                method = 'get';
            }

            var data = $(this).serializeArray();

            $form.data('submiting', true);
            if (Dialog) {
                var msg = $(this).attr('data-form-loading');
                Dialog.loadingOn(msg);
            }
            $.ajax({
                type: method,
                url: action,
                dataType: "json",
                timeout: 10 * 60 * 1000,
                data: data,
                success: function (res) {
                    EventManager.fire('modstart:form.submitted', {
                        $form: $form,
                        res: res,
                    });
                    $form.data('submiting', null);
                    if (Dialog) {
                        Dialog.loadingOff();
                    }
                    return callback(res, {}, Dialog);
                },
                error: function () {
                    var res = {
                        code: -999,
                        msg: "请求出现错误 T_T"
                    }
                    EventManager.fire('modstart:form.submitted', {
                        $form: $form,
                        res: res,
                    });
                    $form.data('submiting', null);
                    if (Dialog) {
                        Dialog.loadingOff();
                    }
                    return callback(res, {}, Dialog);
                }
            });

            return false;
        });
    },
    // 普通表单初始化
    initCommon: function (form, Dialog) {
        var $form = $(form);
        $form.on('submit', function () {

            // 防止重入
            var loadingMsg = $(this).attr('data-form-loading');
            if (loadingMsg && $form.data('submiting')) {
                return false;
            }

            var action = $(this).attr("action");
            var method = $(this).attr("method");
            var data = $(this).serializeArray();
            var callbackValidate = $(this).data('callbackValidate');
            var callback = $(this).data('callback');

            if (callbackValidate && !callbackValidate()) {
                return false;
            }
            $form.data('submiting', true);

            if (Dialog) {
                if ($(this).is('[data-form-loading]')) {
                    Dialog.loadingOn(loadingMsg);
                }
            }

            return true;
        });
    }
};


module.exports = Form;