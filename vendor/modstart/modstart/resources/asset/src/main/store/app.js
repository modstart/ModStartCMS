import {Dialog} from "../../svue/lib/dialog";
import Vue from 'vue'

const app = {
    state: {
        hashPC: null,
        hashLazyValue: null,

        hashUpdateDialog: false,

        userInit: false,
        user: {
            id: 0
        },
        auth: {
            rules: []
        },
        biz: {},

        message: {
            newMessageCount: 0
        },
    },

    mutations: {
        SET_APP_HASH_PC: (state, hashPC) => {
            if (state.hashPC && state.hashPC !== hashPC) {
                if (!state.hashUpdateDialog) {
                    state.hashUpdateDialog = true
                    Dialog.confirm('页面代码有更新，现在刷新页面？', () => {
                        window.location.reload()
                    }, () => {
                        state.hashUpdateDialog = false
                    })
                }
            } else {
                state.hashPC = hashPC
            }
        },
        SET_APP_HASH_LAZY_VALUE: (state, hashLazyValue) => {
            if (state.hashLazyValue !== null) {
                for (let k in hashLazyValue) {
                    if (hashLazyValue[k] !== state.hashLazyValue[k]) {
                        Vue.prototype.$lazystore.update(k)
                        // console.log('update lazy value -> ', k)
                    }
                }
            }
            state.hashLazyValue = hashLazyValue
        },
        SET_APP_USER: (state, user) => {
            state.user = user
            state.userInit = true
        },
        SET_APP_AUTH: (state, auth) => {
            auth.rules = auth.rules
                .map(rule => {
                    let reg = rule.replace(/\*/g, '.+').replace(/\//g, '\\/')
                    return new RegExp("^" + reg + "$")
                })
            state.auth = auth
        },
        SET_APP_BIZ: (state, biz) => {
            state.biz = biz
        },
        SET_APP_MESSAGE: (state, message) => {
            state.message = message
        }
    }
}

export default app
