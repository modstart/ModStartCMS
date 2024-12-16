import Vue from 'vue'

const base = {
    state: {
        config: {},
        lazy: {},
        hashLazyValue: null,
        init: false,
    },

    mutations: {
        SET_CONFIG: (state, data) => {
            state.config = data
            state.init = true
        },
        SET_LAZY: (state, data) => {
            Vue.set(state.lazy, data[0], data[1])
        },
        SET_LAZY_VALUE_HASH: (state, hashLazyValue) => {
            if (state.hashLazyValue !== null) {
                for (let k in hashLazyValue) {
                    if (hashLazyValue[k] !== state.hashLazyValue[k]) {
                        Vue.prototype.$lazystore.update(k)
                    }
                }
            }
            state.hashLazyValue = hashLazyValue
        },
    }
}

export default base
