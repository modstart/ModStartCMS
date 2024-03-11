let $ = require('jquery');

let Util = require('./util.js');

function createDialogHtml(dialogIndex, html) {
    if (!$('#msDialogStyle').length) {
        $('head').append('<style id="msDialogStyle">@keyframes ms-rotate { from { transform: rotate(0) } to { transform: rotate(360deg) } }</style>');
    }
    return '<div data-ms-dialog id="msDialog' + dialogIndex
        + '" style="display:flex;align-items:center;position:fixed;left:0;top:0;width:100%;height:100%;background:rgba(0,0,0,0.01);z-index:' + Util.getNextMaxZIndex() + ';">'
        + '<div style="flex-grow:1;text-align:center;">'
        + html
        + '</div>'
        + '</div>';
}

let Dialog = {
    device: 'pc',
    dialogIndex: 1,
    // 计算提示信息显示时间
    getMsgDuration: function (msg) {
        let ms = 2000;
        if (msg && msg.length > 10) {
            ms = 1000 * parseInt(msg.length / 5);
        }
        return ms;
    },
    // [开始] 这部分的提示需处理
    /**
     * @Util 页面遮罩显示
     * @method MS.dialog.loadingOn
     * @param msg string 提示信息
     * @return index 遮罩的index
     */
    loadingOn: function (msg) {
        msg = msg || null;
        if (window.layer) {
            if (msg) {
                let index = layer.open({
                    type: 1,
                    content: '<div style="padding:10px;height:32px;box-sizing:content-box;"><div class="layui-layer-ico16" style="display:inline-block;margin-right:10px;"></div><div style="display:inline-block;line-height:32px;vertical-align:top;font-size:13px;" class="loading-text">' + msg + '</div></div>',
                    shade: [0.3, '#000'],
                    closeBtn: false,
                    title: false,
                    area: ['auto', 'auto']
                });
                $('#layui-layer' + index).attr('type', 'loading');
                return index
            }
            return layer.load(2);
        }
        let dialogIndex = (Dialog.dialogIndex++)
        let $dialog = $(createDialogHtml(dialogIndex,
            '<div style="animation:ms-rotate 1s infinite;display:inline-block;position:relative;border:3px solid #EEE;border-right-color:#3555CC;border-radius:50%;width:30px;height:30px;vertical-align:middle;margin-right:10px;">'
            + '</div>'
            + '<div data-text style="display:inline-block;">'
            + (msg ? msg : '')
            + '</div>')).appendTo('body');
        return dialogIndex
    },
    /**
     * @Util 页面遮罩更新
     * @method MS.dialog.loadingUpdate
     * @param loading 遮罩的index
     * @param msg string 提示信息
     */
    loadingUpdate: function (loading, msg) {
        if (window.layer) {
            $('#layui-layer' + loading + ' .loading-text').html(msg)
        } else {
            $('#msDialog' + loading).find('[data-text]').html(msg)
        }
        $(window).resize()
    },
    /**
     * @Util 页面遮罩关闭
     * @method MS.dialog.loadingOff
     */
    loadingOff: function () {
        if (window.layer) {
            layer.closeAll('loading');
        } else {
            $('[data-ms-dialog]').remove();
        }
    },
    /**
     * @Util 页面提示成功信息
     * @method MS.dialog.tipSuccess
     * @param msg string 提示信息
     * @param cb function 回调函数
     */
    tipSuccess: function (msg, cb) {
        ms = Dialog.getMsgDuration(msg);
        if (window.layer) {
            layer.msg(msg, {
                shade: 0.01,
                time: ms,
                shadeClose: true,
                offset: '3rem',
                anim: 'slideDown',
                icon: 1,
            }, cb);
        } else {
            let dialogIndex = (Dialog.dialogIndex++)
            let $dialog = $(createDialogHtml(dialogIndex,
                '<div data-text style="display:inline-block;background:rgba(0,0,0,0.5);color:#FFF;padding:10px;border-radius:5px;">'
                + msg
                + '</div>')).appendTo('body');
            setTimeout(function () {
                $dialog.remove();
            }, ms);
        }
    },
    /**
     * @Util 页面提示错误信息
     * @method MS.dialog.tipError
     * @param msg string 提示信息
     * @param cb function 回调函数
     */
    tipError: function (msg, cb) {
        ms = Dialog.getMsgDuration(msg);
        if (window.layer) {
            layer.msg(msg, {
                shade: 0.01,
                time: ms,
                shadeClose: true,
                offset: '3rem',
                anim: 'slideDown',
                icon: 2,
            }, cb);
        } else {
            Dialog.tipSuccess(msg, cb)
        }
    },
    tipPopoverShow: function (ele, msg) {
        let index = $(ele).data('popover-dialog');
        if (index) {
            layer.close(index);
        }
        index = layer.tips(msg, ele, {
            tips: [1, '#333'],
            time: 0,
        });
        $(ele).data('popover-dialog', index);
    },
    tipPopoverHide: function (ele) {
        let index = $(ele).data('popover-dialog');
        if (index) {
            layer.close(index);
            $(ele).data('popover-dialog', null);
        }
    },
    /**
     * @Util 页面提示确认信息
     * @method MS.dialog.tipConfirm
     * @param msg string 提示信息
     * @param callback function 回调函数
     */
    alertSuccess: function (msg, callback) {
        if (window.layer) {
            let index = layer.alert(msg, {icon: 1, closeBtn: 0}, function (index) {
                layer.close(index);
                callback && callback();
            });
            try {
                document.activeElement.blur();
                let $layer = $('#layui-layer' + index);
                let $layerOK = $layer.find('.layui-layer-btn0');
                $layerOK.attr('tabindex', 0).css({outline: 'none'}).get(0).focus();
                $layer.on('keypress', function () {
                    $layerOK.click();
                });
            } catch (e) {
            }
            return index
        }
        alert(msg);
        callback && callback();
    },
    /**
     * @Util 页面提示错误信息
     * @method MS.dialog.tipError
     * @param msg string 提示信息
     * @param callback function 回调函数
     */
    alertError: function (msg, callback) {
        if (window.layer) {
            let index = layer.alert(msg, {icon: 2, closeBtn: 0}, function (index) {
                layer.close(index);
                if (callback) {
                    callback();
                }
            });
            try {
                document.activeElement.blur();
                let $layer = $('#layui-layer' + index);
                let $layerOK = $layer.find('.layui-layer-btn0');
                $layerOK.attr('tabindex', 0).css({outline: 'none'}).get(0).focus();
                $layer.on('keypress', function () {
                    $layerOK.click();
                });
            } catch (e) {
            }
            return index;
        }
        alert(msg);
        callback && callback();
    },
    /**
     * @Util 页面提示确认信息
     * @method MS.dialog.tipConfirm
     * @param msg string 提示信息
     * @param callbackYes function 确认回调函数
     * @param callbackNo function 取消回调函数
     * @param options object 配置参数
     */
    confirm: function (msg, callbackYes, callbackNo, options) {
        options = options || {icon: 3, title: '提示'};
        callbackYes = callbackYes || false;
        callbackNo = callbackNo || false;
        if (window.layer) {
            layer.confirm(msg, options, function (index) {
                layer.close(index);
                callbackYes && callbackYes();
            }, function (index) {
                layer.close(index);
                callbackNo && callbackNo();
            });
        } else {
            if (confirm(msg)) {
                callbackYes && callbackYes();
            } else {
                callbackNo && callbackNo();
            }
        }
    },
    /**
     * @Util 弹出URL页面
     * @method MS.dialog.dialog
     * @param url string 页面URL
     * @param option object 配置参数
     */
    dialog: function (url, option) {
        let opt = $.extend({
            title: null,
            width: '600px',
            height: '80%',
            shadeClose: true,
            openCallback: function (param) {
            },
            closeCallback: function () {
            }
        }, option);
        if (/^\d+px$/.test(opt.width)) {
            if ($(window).width() < parseInt(opt.width)) {
                opt.width = '90%';
                opt.height = '90%';
            }
        }
        return layer.open({
            type: 2,
            title: '正在加载...',
            shadeClose: opt.shadeClose,
            shade: 0.5,
            maxmin: false,
            area: [opt.width, opt.height],
            scrollbar: false,
            content: url,
            success: function (layero, index) {
                opt.openCallback({
                    layero: layero,
                    index: index,
                });
                if (null !== opt.title) {
                    layer.title(opt.title, index);
                    return;
                }
                try {
                    let title = $(layero).find('iframe')[0].contentWindow.document.title;
                    layer.title(Util.specialchars(title), index);
                } catch (e) {
                }
            },
            end: function () {
                opt.closeCallback();
            }
        });
    },
    /**
     * @Util 弹出HTML内容
     * @method MS.dialog.dialogContent
     * @param content string 内容
     * @param option object 配置参数
     */
    dialogContent: function (content, option) {
        let opt = $.extend({
            skin: null,
            closeBtn: true,
            width: 'auto',
            height: 'auto',
            offset: 'auto',
            shade: [0.3, '#000'],
            shadeClose: true,
            fixed: true,
            anim: 0,
            openCallback: function (layero, index) {
            },
            closeCallback: function () {
            }
        }, option);
        // console.log('opt', opt);
        return layer.open({
            skin: opt.skin,
            anim: opt.anim,
            shade: opt.shade,
            offset: opt.offset,
            type: 1,
            title: false,
            zindex: 2019,
            closeBtn: opt.closeBtn,
            shadeClose: opt.shadeClose,
            scrollbar: false,
            content: content,
            area: [opt.width, opt.height],
            fixed: opt.fixed,
            success: function (layero, index) {
                opt.openCallback(layero, index);
            },
            end: function () {
                opt.closeCallback();
            }
        });
    },
    dialogClose: function (index) {
        layer.close(index);
    },
    dialogCloseAll: function () {
        layer.closeAll();
    },
    input: function (callback, option) {
        let opt = $.extend({
            label: '请输入',
            width: '200px',
            height: 'auto',
            defaultValue: ''
        }, option);
        if (/^\d+px$/.test(opt.width)) {
            if ($(window).width() < parseInt(opt.width)) {
                opt.width = $(window).width() - 20 + 'px';
            }
        }
        let value = opt.defaultValue + '';
        let ok = false;
        let inputDialog = Dialog.dialogContent([
            '<div id="dialog-input-box" style="width:', opt.width, ';height:', opt.height, ';background:#FFF;border-radius:3px;">',
            '<div style="padding:10px 10px 0 10px;">', opt.label, '</div>',
            '<div style="padding:10px;"><input type="text" style="border:1px solid #CCC;height:30px;line-height:30px;padding:0 5px;width:100%;display:block;box-sizing:border-box;outline:none;border-radius:2px;" value="', Util.specialchars(opt.defaultValue), '" /></div>',
            '<div style="cursor:pointer;padding:10px;text-align:center;color:#40AFFE;line-height:20px;border-top:1px solid #EEE;cursor:default;" class="ok">确定</div>',
            '</div>'
        ].join(''), {
            openCallback: function () {
                $('#dialog-input-box').find('.ok').on('click', function () {
                    ok = true;
                    Dialog.dialogClose(inputDialog);
                });
                $('#dialog-input-box').find('input').on('change', function () {
                    value = $(this).val();
                });
            },
            closeCallback: function () {
                if (ok) {
                    callback && callback(value);
                }
            }
        });
    },
    preview: function (url, option) {
        option = option || {};
        let opt = $.extend({
            title: null,
            width: 'auto',
            height: 'auto'
        }, option);
        let windowWidth = $(window).width();
        let windowHeight = $(window).height();

        if (Dialog) {
            Dialog.loadingOn();
        }
        let img = new Image();
        img.onerror = function () {
            Dialog.loadingOff();
            Dialog.tipError('Image load error');
        };
        img.onload = function () {
            Dialog.loadingOff();
            let maxWidth = windowWidth - 40;
            let maxHeight = windowHeight - 40;
            let width = img.width, height = img.height;

            if (width > maxWidth) {
                height = parseInt(height * maxWidth / width);
                width = maxWidth;
            }
            if (height > maxHeight) {
                width = parseInt(width * maxHeight / height);
                height = maxHeight;
            }

            let html = [
                '<div style="width:', width, 'px;height:', height, 'px;">',
                '   <img src="', url, '" style="width:', width, 'px;height:', height, 'px;" />',
                '</div>'
            ].join('');
            Dialog.dialogContent(html, opt);
        };
        img.src = url;
        return false;
    }
    // [结束] 这部分的提示需处理
};


module.exports = Dialog;
