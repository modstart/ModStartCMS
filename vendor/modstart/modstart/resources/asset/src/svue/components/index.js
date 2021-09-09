export default (Vue) => {
  Vue.component("smart-link", () => import('./SmartLink'))
  Vue.component("smart-captcha", () => import('./SmartCaptcha'))
  Vue.component("smart-verify", () => import('./SmartVerify'))
}
