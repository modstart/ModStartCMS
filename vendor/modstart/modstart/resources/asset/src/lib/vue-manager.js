// vue 依赖已经在 webpack 中去除，需要外部引入
import Vue from 'vue'
import {StrUtil, UrlUtil, HtmlUtil} from './../svue/lib/util'

const Dialog = window.api.dialog
const Base = window.api.base
Vue.config.productionTip = false

// element-ui 依赖已经在 webpack 中去除，需要外部引入
import ElementUI from 'element-ui';
import vueTimeago from 'vue-timeago';
import VueClipboard from 'vue-clipboard2';
import {EventBus} from '@ModStartAsset/svue/lib/event-bus';

Vue.prototype.L = (name, ...args) => {
    let tpl = name
    if (window.lang && window.lang[name]) {
        tpl = window.lang[name]
    }
    if (args.length) {
        return StrUtil.sprintf(tpl, ...args)
    }
    return tpl
}

Vue.use(ElementUI, {size: 'mini', zIndex: 3000});
Vue.use(vueTimeago, {
    name: 'TimeAgo',
    locale: 'zh_CN',
    locales: {
        'zh_CN': require('date-fns/locale/zh_cn'),
    }
})
Vue.use(VueClipboard)
Vue.prototype.$doCopyText = function (text, tips) {
    tips = tips || this.L('Copy Success')
    this.$copyText(text).then(
        () => Dialog.tipSuccess(tips),
        () => Dialog.tipError(this.L('Copy Fail'))
    );
}
Vue.prototype.$onCopySuccess = () => {
    Dialog.tipSuccess((window.lang && window.lang['Copy Success']) ? window.lang['Copy Success'] : 'Copy Success')
}
Vue.prototype.$onCopyError = () => {
    Dialog.tipError((window.lang && window.lang['Copy Fail']) ? window.lang['Copy Fail'] : 'Copy Fail')
}

Vue.prototype.$highlight = (words, query) => {
    words = HtmlUtil.specialchars(words)
    if (!query) {
        return words
    }
    const iQuery = new RegExp(query, "ig");
    return words.toString().replace(iQuery, function (matchedTxt, a, b) {
        return ('<span data-highlight class="tw-text-red-500">' + matchedTxt + '</span>');
    });
}

import routie from 'webix-routie'

const HashRouter = {
    init(routes) {
        routie(routes)
    },
    to(path) {
        routie(path)
    }
}
Vue.prototype.$hashRouter = HashRouter

const Api = {
    post(url, param, successCB, errorCB) {
        successCB = successCB || Base.defaultFormCallback
        errorCB = errorCB || Base.defaultFormCallback
        Base.post(url, param, function (res) {
            Base.defaultFormCallback(res, {
                success: function (res) {
                    if (true === successCB(res)) {
                        Base.defaultFormCallback(res)
                    }
                },
                error: function (res) {
                    if (true !== errorCB(res)) {
                        Base.defaultFormCallback(res)
                    }
                }
            })
        })
    },
    postRaw(url, param, cb) {
        Base.post(url, param, function (res) {
            cb && cb(res)
        })
    }
}
Vue.prototype.$api = Api
Vue.prototype.$url = {
    buildParam(param) {
        param = param || null
        if (param) {
            let kvs = []
            for (let k in param) {
                kvs.push(UrlUtil.urlencode(k) + '=' + UrlUtil.urldecode(param[k]))
            }
            param = '?' + kvs.join('&')
        }
        return param || ''
    },
    current() {
        const l = window.location
        return `${l.pathname}${l.search}${l.hash}`
    },
    admin(url, param) {
        return `${window.__msAdminRoot}${url}${this.buildParam(param)}`
    },
    web(url, param) {
        return `${window.__msRoot}${url}${this.buildParam(param)}`
    },
    api(url, param) {
        return `${window.__msRoot}api/${url}${this.buildParam(param)}`
    },
    cdn(url) {
        if (url && url.startsWith('/')) {
            url = url.replace(/^[ \/]+/g, '')
        }
        return `${window.__msCDN}${url}`
    }
}
Vue.prototype.$r = {
    to(url) {
        window.location.href = url
    },
    replace(url) {
        window.location.replace = url
    }
}
Vue.prototype.$dialog = Dialog
Vue.prototype.L = function () {
    let name = arguments[0]
    let args = Array.from(arguments)
    args.splice(0, 1)
    if (window.lang && window.lang[name]) {
        if (args.length) {
            return StrUtil.sprintf(name, ...args)
        }
        return window.lang[name]
    }
    if (args.length) {
        return StrUtil.sprintf(name, ...args)
    }

    return name
}

import Vuex from 'vuex'

export const VueManager = {
    Vue,
    Api,
    EventBus,
    makeStore(state, mutations, modules) {
        Vue.use(Vuex)
        return new Vuex.Store({
            modules,
            state: {...state},
            mutations: {...mutations}
        })
    },
    QuickMount(el, template, components, extra, cb) {
        extra = extra || {}
        if (!document.querySelector(el)) {
            return null
        }
        if (!Array.isArray(components)) {
            components = [components]
        }
        let importComponents = {}
        components.forEach(o => {
            importComponents[o.name] = o
        })
        let Bootstrap = null
        if (extra && ('Bootstrap' in extra)) {
            Bootstrap = extra['Bootstrap']
        }
        let app = new Vue({
            el,
            ...extra,
            data() {
                return {
                    loading: true,
                }
            },
            created() {
                Bootstrap && Bootstrap.created(this)
            },
            mounted() {
                this.loading = false
                EventBus.$emit('EventAppMounted')
                Bootstrap && Bootstrap.mounted(this)
            },
            components: importComponents,
            template
        })
        cb && cb(app)
        return app
    }
}
