import {Dialog} from './../lib/dialog'

const reload = {

    state: {
        hashPC: null,
        hashLazyValue: null,
        hashUpdateDialog: false,
    },

    mutations: {
        SET_RELOAD_HASH_PC: (state, hashPC) => {
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
    }
}

export default reload
