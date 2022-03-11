var Dialog = require('./dialogPC.js');

var SelectorDialog = function (option) {
    if (Dialog == undefined) {
        alert('Dialog must defined');
    }
    this.opt = $.extend({
        limitMax: 100,
        limitMin: 1,
        dialogWidth: '940px',
        dialogHeight: '90%',
        server: '/path/to/link/choose/dialog',
        callback: function (items) {
            alert('Select : ' + JSON.stringify(items));
        }
    }, option);
    if ($(window).width() < 800) {
        this.opt.dialogWidth = '90%';
    }
    this.dialog = Dialog;
    this.runtime = {
        dialog: null
    };
};

SelectorDialog.prototype.show = function () {
    var me = this;
    window.__selectorDialogOption = me.opt
    window.__dialogSelectIds = [];
    window.__selectorDialogItems = [];
    this.runtime.dialog = this.dialog.dialog(this.opt.server, {
        width: this.opt.dialogWidth,
        height: this.opt.dialogHeight,
        closeCallback: function () {
            var items = window.__selectorDialogItems;
            if (!items.length) {
                return;
            }
            if (items.length > me.opt.limitMax) {
                me.dialog.tipError('Select limit max ' + me.opt.limitMax + ' item(s)');
                return;
            } else if (items.length < me.opt.limitMin) {
                me.dialog.tipError('Select limit min ' + me.opt.limitMin + ' item(s)');
                return;
            }
            me.opt.callback(items);
        }
    });
    return me;
};

SelectorDialog.prototype.close = function () {
    this.dialog.dialogClose(this.runtime.dialog);
};

module.exports = SelectorDialog;

