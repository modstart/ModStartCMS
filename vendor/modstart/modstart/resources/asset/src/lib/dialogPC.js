let $ = require('jquery');

if ('layer' in window) {
    console.error('ERR: dialog should required only once, use window.api.dialog instead')
}

let layer = require('./layer/layer.js');
let layerLess = require('./layer/theme/default/layer.less');
let Util = require('./util.js');

let Dialog = {
    device: 'pc',
    // [开始] 这部分的提示需处理
    loadingOn: function (msg) {
        msg = msg || null;
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
        } else {
            return layer.load(2);
        }
    },
    loadingUpdate: function (loading, msg) {
        $('#layui-layer' + loading + ' .loading-text').html(msg)
        $(window).resize()
    },
    loadingOff: function () {
        layer.closeAll('loading');
    },
    tipSuccess: function (msg, cb) {
        let ms = 2000;
        if (msg && msg.length > 10) {
            ms = 1000 * parseInt(msg.length / 5);
        }
        layer.msg(msg, {shade: 0.3, time: ms, shadeClose: true, anim: -1}, cb);
    },
    tipError: function (msg, cb) {
        let ms = 2000;
        if (msg.length > 10) {
            ms = 1000 * parseInt(msg.length / 5);
        }
        layer.msg(msg, {shade: 0.3, time: ms, shadeClose: true, anim: 6}, cb);
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
    alertSuccess: function (msg, callback) {
        layer.alert(msg, {icon: 1, closeBtn: 0}, function (index) {
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
    },
    alertError: function (msg, callback) {
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
    },
    confirm: function (msg, callbackYes, callbackNo, options) {
        options = options || {icon: 3, title: '提示'};
        callbackYes = callbackYes || false;
        callbackNo = callbackNo || false;
        layer.confirm(msg, options, function (index) {
            layer.close(index);
            if (callbackYes) {
                callbackYes();
            }
        }, function (index) {
            layer.close(index);
            if (callbackNo) {
                callbackNo();
            }
        });
    },
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
