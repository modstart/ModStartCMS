const jquery = require('jquery');
const Base = require('./../lib/basePC');
const Dialog = require('./../lib/dialogPC');
const Lister = require('./../lib/lister');
const Util = require('./../lib/util');
const SelectorDialog = require('./../lib/selectorDialog');

const Header = {
    trigger: function (selector, showClass) {
        selector = selector || 'header'
        showClass = showClass || 'show'
        var $header = $(selector)
        if ($header.hasClass(showClass)) {
            $header.removeClass(showClass)
        } else {
            $header.addClass(showClass)
        }
        // 页面部分组件在手机上会出现被系统自动置顶的情况（比如Video），这时候需要自动隐藏
        $('[data-header-shown-auto-hide]').css('visibility', $header.hasClass(showClass) ? 'hidden' : 'visible')
    },
    hide: function (selector, showClass) {
        selector = selector || 'header'
        showClass = showClass || 'show'
        var $header = $(selector)
        $header.removeClass(showClass)
        // 页面部分组件在手机上会出现被系统自动置顶的情况（比如Video），这时候需要自动隐藏
        $('[data-header-shown-auto-hide]').css('visibility', 'visible')
    }
}

const MS = {
    ready() {
        let args = Array.from(arguments)
        const cb = args.pop()
        let passed = true
        for (let f of args) {
            switch (typeof f) {
                case 'function':
                    if (!f()) passed = false
                    break
                default:
                    if (!f) passed = false
            }
            if (!passed) break
        }
        if (!passed) {
            setTimeout(() => {
                MS.ready.call(this, ...arguments)
            }, 50)
            return
        }
        cb()
    },
    dialog: Dialog,
    util: Util,
    api: {
        defaultCallback: Base.defaultFormCallback,
        post: Base.post
    },
    selectorDialog: SelectorDialog,
    header: Header
}

window.api = window.api || {}

window.api.jquery = jquery
window.api.base = Base
window.api.dialog = Dialog
window.api.lister = Lister
window.api.selectorDialog = SelectorDialog
window.api.util = Util

Base.init()

window.MS = MS
