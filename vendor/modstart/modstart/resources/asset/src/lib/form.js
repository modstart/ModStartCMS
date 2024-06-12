var $ = require('jquery');
var Util = require('./util.js');
var EventManager = require('./event-manager.js');

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

var Form = {
    defaultTimeout: 10 * 60 * 1000, delaySubmit: function ($form, cb) {
        var pass = true;
        $form.find('[data-form-process]').each(function (i, o) {
            if ($(o).attr('data-form-process') == 'processing') {
                pass = false;
            }
        });
        if (pass) {
            cb();
        } else {
            setTimeout(function () {
                Form.delaySubmit($form, cb);
            }, 100);
        }
    },
    responseToRes: function (response) {
        var res = {
            code: -999, msg: "请求出现错误 T_T"
        }
        // console.log('error', response);
        if ('timeout' === response.statusText) {
            res.msg = '请求超时取消 T_T';
        } else {
            res.msg = '请求出现错误(' + response.status + ' ' + response.statusText + ') T_T';
        }
        return res;
    },
    redirectProcess: function (redirect) {
        if (!redirect) {
            return;
        }
        if ('[reload]' === redirect) {
            winReload(window);
        } else if ('[root-reload]' == redirect) {
            winReload(Util.getRootWindow())
        } else if ('[back]' === redirect) {
            window.history.back();
        } else if ('[tab-close]' === redirect) {
            window._pageTabManager.closeFromTab()
        } else if (redirect.indexOf('[js]') === 0) {
            // console.log('eval', redirect.substr(4));
            eval(redirect.substr(4));
        } else if (redirect.indexOf('[ijs]') === 0) {
            // run js instant
            eval(redirect.substr(5));
        } else if (redirect.indexOf('[iurl]') === 0) {
            window.location.href = redirect.substr(6);
        } else {
            window.location.href = redirect;
        }
    },
    defaultCallback: function (res, callback, Dialog) {

        Dialog = Dialog || null;

        callback = callback || {};

        if (typeof res !== 'object') {
            res = {code: -999, msg: res};
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

        var defaultSuccessFunc = function () {
            if (msg) {
                if (redirect) {
                    if (Dialog) {
                        if (redirect && (
                            redirect.indexOf('[ijs]') === 0
                            ||
                            redirect.indexOf('[iurl]') === 0
                        )) {
                            Dialog.tipSuccess(msg);
                            setTimeout(function () {
                                Form.redirectProcess(redirect);
                            }, Dialog.getMsgDuration(msg));
                        } else {
                            Dialog.alertSuccess(msg, function () {
                                Form.redirectProcess(redirect);
                            });
                        }
                    } else {
                        alert(msg);
                        Form.redirectProcess(redirect);
                    }
                } else {
                    if (Dialog) {
                        Dialog.tipSuccess(msg);
                    } else {
                        alert(msg);
                    }
                }
            } else {
                if (redirect) {
                    Form.redirectProcess(redirect);
                }
            }
        }

        var successFunc = function () {
            if ("success" in callback) {
                if (true === callback.success(res)) {
                    defaultSuccessFunc();
                }
            } else {
                defaultSuccessFunc();
            }
        };

        var defaultErrorFunc = function () {
            if (msg) {
                if (redirect) {
                    if (Dialog) {
                        if (redirect && redirect.indexOf('[ijs]') === 0) {
                            Dialog.tipError(msg);
                            setTimeout(function () {
                                Form.redirectProcess(redirect);
                            }, Dialog.getMsgDuration(msg));
                        } else {
                            Dialog.alertError(msg, function () {
                                Form.redirectProcess(redirect);
                            });
                        }
                    } else {
                        alert(msg);
                        Form.redirectProcess(redirect);
                    }
                } else {
                    if (Dialog) {
                        Dialog.tipError(msg);
                    } else {
                        alert(msg);
                    }
                }
            } else {
                if (redirect) {
                    Form.redirectProcess(redirect);
                }
            }
        };

        var errorFunc = function () {
            if ("error" in callback) {
                if (true !== callback.error(res)) {
                    defaultErrorFunc()
                }
            } else {
                defaultErrorFunc()
            }
        };

        if (0 == code) {
            successFunc();
        } else {
            if (1002 == code) {
                EventManager.fire('modstart:captcha.error', {
                    res: res
                });
            }
            errorFunc();
        }
    }, // ajax表单初始化
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

            $form.data('submiting', true);
            if (Dialog) {
                var msg = $(this).attr('data-form-loading');
                Dialog.loadingOn(msg);
            }
            var $this = $(this);
            // console.log('form', method, action);
            Form.delaySubmit($form, function () {
                var data = $this.serializeArray();
                $.ajax({
                    type: method,
                    url: action,
                    dataType: "json",
                    timeout: Form.defaultTimeout,
                    data: data,
                    success: function (res) {
                        // console.log('success', res);
                        EventManager.fire('modstart:form.submitted', {
                            $form: $form, res: res,
                        });
                        $form.data('submiting', null);
                        if (Dialog) {
                            Dialog.loadingOff();
                        }
                        return callback(res, {}, Dialog);
                    },
                    error: function (res) {
                        res = Form.responseToRes(res);
                        EventManager.fire('modstart:form.submitted', {
                            $form: $form, res: res,
                        });
                        $form.data('submiting', null);
                        if (Dialog) {
                            Dialog.loadingOff();
                        }
                        return callback(res, {}, Dialog);
                    }
                });
            });

            return false;
        });
    }, // 普通表单初始化
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
