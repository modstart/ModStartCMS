import axios from 'axios'
import {Message} from 'element-ui'

let apiRequest = null, apiStore = null

const isInited = () => {
    return apiStore && apiStore.state.api.baseUrl
}

const isRemoteInited = () => {
    return apiStore && apiStore.state.api.baseUrl &&
        (
            apiStore.state.api.baseUrl.startsWith('http://')
            ||
            apiStore.state.api.baseUrl.startsWith('https://')
        )
}

const init = (store) => {
    if (null !== apiRequest) {
        return
    }
    apiStore = store
    apiRequest = axios.create({
        baseURL: apiStore ? apiStore.state.api.baseUrl : '',
        timeout: 60 * 1000
    })
    apiRequest.interceptors.request.use(
        config => {
            if (apiStore) {
                let token = apiStore.state.api.token
                if (token) {
                    config.headers[apiStore.state.api.tokenKey] = token
                }
                config.baseURL = apiStore.state.api.baseUrl
                // let additionalSendHeaders = Storage.getObject('ADDITIONAL_HEADERS', {})
                // for (let k in additionalSendHeaders) {
                //     config.headers[k] = additionalSendHeaders[k]
                // }
            }
            config.headers['is-ajax'] = true
            return config
        },
        error => {
            Promise.reject(error)
        }
    )
    apiRequest.interceptors.response.use(
        response => {
            if (apiStore) {
                try {
                    if (response.headers[apiStore.state.api.tokenKey]) {
                        apiStore.commit('SET_API_TOKEN', response.headers[apiStore.state.api.tokenKey])
                    }
                } catch (e) {
                }
            }
            return response.data
        },
        error => {
            return Promise.reject(error)
        }
    )
}

const processResponse = (res, failCB, successCB) => {
    if (typeof (res) === 'string' || !('code' in res)) {
        processResponse({code: -1, msg: '请求失败(2):' + res}, failCB, successCB)
        console.error('error -> ', typeof (res), res)
        return
    }
    if (res.code) {
        let processed = false
        if (apiStore) {
            for (let processor of apiStore.state.api.codeProcessors) {
                if (processor.code === res.code) {
                    const result = processor.callback.call(null, res)
                    if (result === true) {
                        processed = true
                    }
                }
            }
        }
        if (!processed) {
            // 只有返回 true 表示已经处理了响应
            if (true !== failCB(res)) {
                Message({
                    message: res.msg,
                    type: 'error',
                    duration: 5 * 1000
                })
            }
        }
    } else {
        // 只有返回 true 表示需要处理响应
        if (true === successCB(res)) {
            Message({
                message: res.msg,
                type: 'success',
                duration: 5 * 1000
            })
        }
    }
}

const defaultFailCallback = function (res) {
    Message({
        message: res.msg,
        type: 'error',
        duration: 5 * 1000
    })
    return true
}

const defaultSuccessCallback = function (res) {
    if (res.msg) {
        Message({
            message: res.msg,
            type: 'success',
            duration: 5 * 1000
        })
    }
}

const defaultErrorCatcher = function (err, failCB) {
    const errString = err.toString()
    const res = {code: -1, msg: '请求失败(1):' + err}
    if (errString.includes('failed with status code 404')) {
        res.msg = '请求失败：地址不存在'
    }
    const ret = failCB(res)
    if (undefined === ret) {
        defaultFailCallback(res)
    }
    console.error('api -> ', err)
}

/**
 *
 * @param url
 * @param param
 * @param successCallback : 如果需要系统默认处理   返回 true
 * @param failCallback    : 如果不需要系统默认处理 返回 true
 * @returns {Promise<AxiosResponse<any>>}
 */
// const postForm = (url, param, successCallback, failCallback) => {
//     const failCB = failCallback || defaultFailCallback
//     const successCB = successCallback || defaultSuccessCallback
//     const post = new FormData()
//     if (param) {
//         Object.keys(param).forEach((i) => {
//             post.append(i, param[i])
//         })
//     }
//     return apiRequest.post(url, post)
//         .then(res => processResponse(res, failCB, successCB))
//         .catch(err => defaultErrorCatcher(err, failCB))
// }

/**
 *
 * @param url
 * @param param
 * @param successCallback : 如果需要系统默认处理   返回 true
 * @param failCallback    : 如果不需要系统默认处理 返回 true
 * @returns {Promise<AxiosResponse<any>>}
 */
const post = (url, param, successCallback, failCallback) => {
    const failCB = failCallback || defaultFailCallback
    const successCB = successCallback || defaultSuccessCallback
    const config = {
        headers: {
            'Content-Type': 'application/json'
        }
    }
    return apiRequest.post(url, JSON.stringify(param), config)
        .then(res => processResponse(res, failCB, successCB))
        .catch(err => defaultErrorCatcher(err, failCB))
}

const Api = {
    isInited,
    isRemoteInited,
    init,
    post,
    // postJson,
    // setApiBaseUrl,
    // getApiTokenKey,
    // setApiTokenKey
}
//
// /**
//  *
//  * @param url
//  * @param param
//  * @param successCallback : 如果需要系统默认处理   返回 true
//  * @param failCallback    : 如果不需要系统默认处理 返回 true
//  * @returns {Promise<AxiosResponse<any>>}
//  */
// const get = (url, param, successCallback, failCallback) => {
//     const failCB = failCallback || defaultFailCallback
//     const successCB = successCallback || defaultSuccessCallback
//     return apiRequest.get(url, {
//         params: param
//     })
//         .then(res => processResponse(res, failCB, successCB))
//         .catch(err => defaultErrorCatcher(err, failCB))
// }

export {
    Api
}
