import Vue from 'vue'

const auth = {
    state: {
        init: false,
        rules: [],
    },
    mutations: {
        SET_AUTH_RULES: (state, rules) => {
            rules = rules.map(rule => {
                let reg = rule.replace(/\*/g, '.+').replace(/\//g, '\\/')
                return new RegExp("^" + reg + "$")
            })
            state.rules = rules
            state.init = true
        },
    }
}

const hasAuth = (rule) => {
    if (typeof rule == 'object') {
        let match = false
        rule.forEach(o => {
            if (auth.state.rules.filter(reg => reg.test(o)).length > 0) {
                match = true
            }
        })
        return match
    } else if (auth.state.rules.filter(reg => reg.test(rule)).length > 0) {
        return true
    }
    return false
}

const authRouteJump = (routeList) => {
    if (!auth.state.init) {
        setTimeout(() => {
            authRouteJump(routeList)
        }, 100)
        return
    }
    for (let i = 0; i < routeList.length; i++) {
        if (hasAuth(routeList[i][0])) {
            Vue.prototype.$r.replace(routeList[i][1])
            break
        }
    }
}

export {hasAuth, authRouteJump}

export default auth
