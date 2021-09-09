import axios from 'axios'
import Cookies from 'js-cookie'
import store from "../../main/store"
import {Code} from "../../main/constant";
import {Dialog} from "./dialog-mobile";

const request = axios.create({
    baseURL: '/xxx/',
    timeout: 60 * 1000
})

request.interceptors.request.use(
    config => {
        let token = Cookies.get(store.state.api.tokenKey)
        if (token) {
            config.headers[store.state.api.tokenKey] = token
        }
        return config
    },
    error => {
        Promise.reject(error)
    }
)

request.interceptors.response.use(
    response => {
        try {
            if (response.headers[store.state.api.tokenKey]) {
                store.commit('SET_APP_TOKEN', response.headers[store.state.api.tokenKey])
            }
        } catch (e) {
        }
        return response.data
    },
    error => {
        Dialog.tipError(error.message)
        return Promise.reject(error)
    }
)

const processResponse = function (res, failCB, successCB) {
    if (typeof (res) === 'string' || !('code' in res)) {
        processResponse({code: -1, msg: '请求失败:' + res}, failCB, successCB)
        return
    }
    if (res.code) {
        if (res.code === Code.LOGIN_REQUIRED) {
            Dialog.confirm('你已被登出，可以取消继续留在该页面，或者重新登录', () => {
                store.commit('SET_APP_USER', {id: 0})
                store.commit('SET_APP_TOKEN', '')
                location.reload()
            }, () => {
                store.commit('SET_APP_USER', {id: 0})
                store.commit('SET_APP_TOKEN', '')
                location.reload()
            })
        } else {
            if (!failCB(res)) {
                Dialog.tipError(res.msg)
            }
        }
    } else {
        if (successCB(res)) {
            Dialog.tipError(res.msg)
        }
    }
}

const defaultFailCallback = function (res) {
    Dialog.tipError(res.msg)
    return true
}

const defaultSuccessCallback = function (res) {
    if (res.msg) {
        Dialog.tipError(res.msg)
    }
}

const defaultErrorCatcher = function (err, failCB) {
    failCB({code: -1, msg: '请求失败:' + err})
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
const post = (url, param, successCallback, failCallback) => {
    const failCB = failCallback || defaultFailCallback
    const successCB = successCallback || defaultSuccessCallback
    const post = new FormData()
    if (param) {
        Object.keys(param).forEach((i) => {
            post.append(i, param[i])
        })
    }
    return request.post(url, post)
        .then(res => processResponse(res, failCB, successCB))
        .catch(err => defaultErrorCatcher(err, failCB))
}

/**
 *
 * @param url
 * @param param
 * @param successCallback : 如果需要系统默认处理   返回 true
 * @param failCallback    : 如果不需要系统默认处理 返回 true
 * @returns {Promise<AxiosResponse<any>>}
 */
const postJson = (url, param, successCallback, failCallback) => {
    const failCB = failCallback || defaultFailCallback
    const successCB = successCallback || defaultSuccessCallback
    const config = {
        headers: {
            'Content-Type': 'application/json'
        }
    }
    return request.post(url, JSON.stringify(param), config)
        .then(res => processResponse(res, failCB, successCB))
        .catch(err => defaultErrorCatcher(err, failCB))
}

/**
 *
 * @param url
 * @param param
 * @param successCallback : 如果需要系统默认处理   返回 true
 * @param failCallback    : 如果不需要系统默认处理 返回 true
 * @returns {Promise<AxiosResponse<any>>}
 */
const get = (url, param, successCallback, failCallback) => {
    const failCB = failCallback || defaultFailCallback
    const successCB = successCallback || defaultSuccessCallback
    return request.get(url, {
        params: param
    })
        .then(res => processResponse(res, failCB, successCB))
        .catch(err => defaultErrorCatcher(err, failCB))
}

export const Api = {
    get,
    post,
    postJson
}
