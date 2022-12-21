var $ = require('jquery');
var Form = require('./form.js');

var inited = false;

var Convenient = {
    initAll: function (Dialog) {

        // 确定框
        $(document).on('click', '[data-confirm]', function () {
            if ($(this).is('[data-ajax-request]')) {
                return;
            }
            var url = $(this).attr('data-href');
            var newWindow = $(this).is('[data-new-window]');
            Dialog.confirm($(this).attr('data-confirm'), function () {
                if (newWindow) {
                    window.open(url, '_blank');
                } else {
                    window.location.href = url;
                }
            });
            return false;
        });

        $(document).on('click', '[data-ajax-request]', function () {
            var url = $(this).attr('data-ajax-request');
            var confirm = $(this).attr('data-confirm');
            var loading = $(this).is('[data-ajax-request-loading]');
            var method = $(this).attr('data-method');
            var jsonStr = $(this).attr('data-request');
            var jsonp = $(this).is('[data-ajax-jsonp]');
            var callback = $(this).data('callback');
            if (!method) {
                method = 'post';
            }
            if (!callback) {
                callback = Form.defaultCallback;
            }

            var data = {};

            if (jsonStr) {
                try {
                    data = JSON.parse(jsonStr);
                } catch (e) {
                    try {
                        data = eval('data = ' + jsonStr + ';');
                    } catch (e) {
                    }
                }
            } else {
                data = {};
            }

            var sendRequest = function () {
                if (loading) {
                    Dialog.loadingOn();
                }
                $.ajax({
                    async: true,
                    url: url,
                    type: method ? method : 'get',
                    dataType: jsonp ? 'jsonp' : 'json',
                    data: data,
                    success: function (res) {
                        if (loading) {
                            Dialog.loadingOff();
                        }
                        callback(res, {}, Dialog);
                    },
                    error: function (xhr) {
                        if (loading) {
                            Dialog.loadingOff();
                        }
                        alert("请求出错");
                    }
                });
            };
            if (confirm) {
                Dialog.confirm(confirm, function () {
                    sendRequest();
                });
            } else {
                sendRequest();
            }
            return false;
        });

        $(document).on('click', '[data-dialog-request]', function () {
            var $this = $(this);
            var url = $this.attr('data-dialog-request');
            var title = $this.attr('data-dialog-title') || null;
            var option = {
                title: title,
                shadeClose: false,
                closeCallback: function () {
                    $this.trigger('dialog.close', [$this]);
                }
            };
            if ($this.attr('data-dialog-width')) {
                option.width = $this.attr('data-dialog-width');
            }
            if ($this.attr('data-dialog-height')) {
                option.height = $this.attr('data-dialog-height');
            }
            Dialog.dialog(url, option);
        });

        $(document).on('click', '[data-image-preview]', function () {
            var option = {};
            if ($(this).attr('data-option')) {
                eval('option = ' + $(this).attr('data-option'));
            }
            var opt = $.extend({
                title: null,
                width: 'auto',
                height: 'auto'
            }, option);
            var imgUrl = $(this).attr('href');
            if (!imgUrl || imgUrl === 'javascript:;' || imgUrl === '#') {
                imgUrl = $(this).attr('data-image-preview');
            }
            if (!imgUrl) {
                imgUrl = $(this).attr('src');
            }
            var windowWidth = $(window).width();
            var windowHeight = $(window).height();

            if (Dialog) {
                Dialog.loadingOn();
            }
            var img = new Image();
            img.onerror = function () {
                if (Dialog) {
                    Dialog.loadingOff();
                    Dialog.tipError('加载图片出错');
                } else {
                    alert('加载图片出错');
                }
            };
            img.onload = function () {
                if (Dialog) {
                    Dialog.loadingOff();
                }
                var maxWidth = windowWidth - 40;
                var maxHeight = windowHeight - 40;
                var width = img.width, height = img.height;

                if (width > maxWidth) {
                    height = parseInt(height * maxWidth / width);
                    width = maxWidth;
                }
                if (height > maxHeight) {
                    width = parseInt(width * maxHeight / height);
                    height = maxHeight;
                }

                var html = [
                    '<div style="width:', width, 'px;height:', height, 'px;">',
                    '   <img src="', imgUrl, '" style="width:', width, 'px;height:', height, 'px;" />',
                    '</div>'
                ].join('');
                Dialog.dialogContent(html, opt);
            };
            img.src = imgUrl;
            return false;
        });

    },
    initSmartImage: function () {
        $(document).ready(function () {
            setTimeout(function () {
                $('[data-smart-image]').each(function (i, o) {
                    $(o).attr('src', $(o).attr('data-smart-image'));
                });
            }, 0);
        });
    },
    initTip: function (Dialog) {
        Dialog = Dialog || null;

        $(document).on('click', '[data-tip-error]', function () {
            if (Dialog) {
                Dialog.tipError($(this).attr('data-tip-error'));
            } else {
                alert($(this).attr('data-tip-error'));
            }
            return false;
        });

        $(document).on('click', '[data-tip-success]', function () {
            if (Dialog) {
                Dialog.tipError($(this).attr('data-tip-success'));
            } else {
                alert($(this).attr('data-tip-success'));
            }
            return false;
        });

        $(document)
            .on('mouseenter', '[data-tip-popover]', function (e) {
                var msg = $(this).attr('data-tip-popover');
                var ele = this;
                if (Dialog) {
                    Dialog.tipPopoverShow(ele, msg);
                } else {
                    $(ele).attr('title', msg);
                }
            })
            .on('mouseleave', '[data-tip-popover]', function (e) {
                var ele = this;
                if (Dialog) {
                    Dialog.tipPopoverHide(ele);
                }
            });

    }
};

Convenient.init = function (Dialog) {
    if (inited) {
        return;
    }

    inited = true;
    Convenient.initAll(Dialog);
    Convenient.initSmartImage();
    Convenient.initTip(Dialog);
};

module.exports = Convenient;
