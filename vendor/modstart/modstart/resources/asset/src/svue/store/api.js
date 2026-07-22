import Cookies from 'js-cookie'

/**
 * 获取初始 token
 * 优先级：window._data.apiToken（页面注入）> localStorage > Cookies（原有兼容）> null
 * 若页面注入了 apiToken，同时写入 localStorage 供后续页面复用
 */
const _getInitialToken = (tokenKey) => {
    if (window._data && window._data.apiToken) {
        try { localStorage.setItem(tokenKey, window._data.apiToken) } catch (e) {}
        return window._data.apiToken
    }
    try {
        const stored = localStorage.getItem(tokenKey)
        if (stored) return stored
    } catch (e) {}
    return Cookies.get(tokenKey) || null
}

const api = {

    state: {
        baseUrl: '/',
        tokenKey: 'api-token',
        token: _getInitialToken('api-token'),
        codeProcessors: [
            {
                code: 1000,
                callback: () => {
                    console.log('not logined')
                    return true
                }
            }
        ]
    },

    mutations: {
        SET_API_BASE_URL: (state, baseUrl) => {
            state.baseUrl = baseUrl
        },
        SET_API_TOKEN_KEY: (state, tokenKey) => {
            state.tokenKey = tokenKey
            state.token = _getInitialToken(tokenKey)
        },
        SET_API_TOKEN: (state, token) => {
            state.token = token
            Cookies.set(state.tokenKey, token, {expires: new Date((new Date()).getTime() + (3600 * 1000))})
            try { localStorage.setItem(state.tokenKey, token) } catch (e) {}
        },
    }
}

export default api
