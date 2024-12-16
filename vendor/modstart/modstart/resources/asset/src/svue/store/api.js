import Cookies from 'js-cookie'

const api = {

    state: {
        baseUrl: '/',
        tokenKey: 'api-token',
        token: Cookies.get('api-token'),
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
            state.token = Cookies.get(state.tokenKey)
        },
        SET_API_TOKEN: (state, token) => {
            state.token = token
            Cookies.set(state.tokenKey, token, {expires: new Date((new Date()).getTime() + (3600 * 1000))})
        },
    }
}

export default api
