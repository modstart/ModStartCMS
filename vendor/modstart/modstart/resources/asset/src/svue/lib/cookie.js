import Cookies from 'js-cookie'

const Cookie = {
  set(key, value) {
    Cookies.set(key, value);
  },
  get(key) {
    return Cookies.get(key)
  }
}


export {
  Cookie
}
