import Cookies from 'js-cookie'

const Cookie = {
    set(key, value, attributes) {
        Cookies.set(key, value, attributes);
    },
    get(key) {
        return Cookies.get(key)
    },
    remove(key) {
        Cookies.remove(key)
    }
}


export {
    Cookie
}
