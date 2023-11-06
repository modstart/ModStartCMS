
const sprintf = require('sprintf-js').sprintf

export const StrUtil = {
    randomString(len) {
        len = len || 32;
        var $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        var maxPos = $chars.length;
        var pwd = '';
        for (let i = 0; i < len; i++) {
            pwd += $chars.charAt(Math.floor(Math.random() * maxPos));
        }
        return pwd;
    },
    matchWildcard(text, pattern) {
        var escapeRegex = (str) => str.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1")
        pattern = pattern.split("*").map(escapeRegex).join(".*")
        pattern = "^" + pattern + "$"
        var regex = new RegExp(pattern)
        return regex.test(text)
    },
    keywordsMatchWildcard(text, pattern) {
        var escapeRegex = (str) => str.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1")
        pattern = pattern.split("*").map(escapeRegex).join(".*")
        var regex = new RegExp(pattern)
        return regex.test(text)
    },
    sprintf() {
        const args = Array.from(arguments)
        return sprintf.call(null, ...args)
    }
}

