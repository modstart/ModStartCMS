const jquery = require('jquery');
const Base = require('./../lib/basePC');
const Dialog = require('./../lib/dialogPC');
const Lister = require('./../lib/lister');
const Util = require('./../lib/util');
const SelectorDialog = require('./../lib/selectorDialog');
import {Tree} from './../svue/lib/tree';

const sprintf = require('sprintf-js').sprintf;

jquery.fn.isInViewport = function () {
    var elementTop = $(this).offset().top;
    var elementBottom = elementTop + $(this).outerHeight();

    var viewportTop = $(window).scrollTop();
    var viewportBottom = viewportTop + $(window).height();

    return elementBottom > viewportTop && elementTop < viewportBottom;
};

const Header = {
    trigger: function (ele, selector, showClass) {
        selector = selector || 'header'
        showClass = showClass || 'show'
        if ((typeof ele === 'undefined') && window.event) {
            ele = window.event.target
        }
        var $header = ele ? ($(ele).closest(selector)) : ($(selector))
        if ($header.hasClass(showClass)) {
            $header.removeClass(showClass)
            $('html').removeClass('body-scroll-lock')
        } else {
            $header.addClass(showClass)
            $('html').addClass('body-scroll-lock')
        }
    },
    hide: function (ele, selector, showClass) {
        if ((typeof ele === 'undefined') && window.event) {
            ele = window.event.target
        }
        selector = selector || 'header'
        showClass = showClass || 'show'
        var $header = ele ? ($(ele).closest(selector)) : ($(selector))
        $header.removeClass(showClass)
        $('html').removeClass('body-scroll-lock')
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
        post: Base.post,
        postSuccess: Base.postSuccess
    },
    selectorDialog: SelectorDialog,
    header: Header,
    tree: Tree,
    L: function () {
        var lang = arguments[0]
        if (MS.trans && (lang in MS.trans)) {
            arguments[0] = MS.trans[lang]
            return sprintf.call(null, ...arguments)
        }
        return sprintf.call(null, ...arguments)
    }
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
