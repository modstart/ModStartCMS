const jquery = require('jquery');
const Base = require('./../lib/base');
const Form = require('./../lib/form');
const Dialog = require('./../lib/dialog');
const Lister = require('./../lib/lister');
const Util = require('./../lib/util');
const Url = require('./../lib/url');
const Ui = require('./../lib/ui');
const EventManager = require('./../lib/event-manager');
const SelectorDialog = require('./../lib/selectorDialog');
import {Tree} from './../svue/lib/tree';
import {Storage} from './../svue/lib/storage';
import {FileUtil} from './../svue/lib/file-util';
import {DateUtil} from './../svue/lib/date-util';
import {ImageUtil} from './../svue/lib/image-util';
import {Collection} from './../svue/lib/collection';

const sprintf = require('sprintf-js').sprintf;

jquery.fn.isInViewport = function () {
    var elementTop = $(this).offset().top;
    var elementBottom = elementTop + $(this).outerHeight();

    var viewportTop = $(window).scrollTop();
    var viewportBottom = viewportTop + $(window).height();

    return elementBottom > viewportTop && elementTop < viewportBottom;
};

jquery.fn.serializeJson = function () {
    var serializeObj = {};
    var array = this.serializeArray();
    var str = this.serialize();
    $(array).each(function () {
        if (serializeObj[this.name]) {
            if ($.isArray(serializeObj[this.name])) {
                serializeObj[this.name].push(this.value);
            } else {
                serializeObj[this.name] = [serializeObj[this.name], this.value];
            }
        } else {
            serializeObj[this.name] = this.value;
        }
    });
    return serializeObj;
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
    },
}

const Dom = {
    insertText(ele, text) {
        if (typeof ele === 'string') {
            ele = document.querySelector(ele)
        }
        //IE support
        if (document.selection) {
            ele.focus();
            sel = document.selection.createRange();
            sel.text = text;
        }
        //MOZILLA and others
        else if (ele.selectionStart || ele.selectionStart == '0') {
            var startPos = ele.selectionStart;
            var endPos = ele.selectionEnd;
            ele.value = ele.value.substring(0, startPos)
                + text
                + ele.value.substring(endPos, ele.value.length);
        } else {
            ele.value += text;
        }
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
    ui: Ui,
    dom: Dom,
    dialog: Dialog,
    util: Util,
    form: Form,
    file: FileUtil,
    date: DateUtil,
    image: ImageUtil,
    collection: Collection,
    api: {
        defaultCallback: Base.defaultFormCallback,
        post: Base.post,
        postSuccess: Base.postSuccess
    },
    selectorDialog: SelectorDialog,
    header: Header,
    tree: Tree,
    url: Url,
    storage: Storage,
    eventManager: EventManager,
    L: function () {
        var lang = arguments[0]
        if (MS.trans && (lang in MS.trans)) {
            arguments[0] = MS.trans[lang]
            return sprintf.call(null, ...arguments)
        }
        return sprintf.call(null, ...arguments)
    }
}

function init() {
    var windowScrollFar = false
    $(window).scroll(function () {
        var scrollTop = $(window).scrollTop()
        if (scrollTop > 60 * 3) {
            if (!windowScrollFar) {
                windowScrollFar = true
                $('html').addClass('body-scroll-far')
            }
        } else if (scrollTop < 60) {
            if (windowScrollFar) {
                windowScrollFar = false
                $('html').removeClass('body-scroll-far')
            }
        }
    });
}

window.api = window.api || {}


window.api.jquery = jquery
window.api.base = Base
window.api.dialog = Dialog
window.api.lister = Lister
window.api.selectorDialog = SelectorDialog
window.api.util = Util

Base.init()
init()

window.MS = MS
