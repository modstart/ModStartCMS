var pinyin = require("pinyin");
// https://www.npmjs.com/package/pinyin

export const PinyinUtil = {
    zh2pinyin(text) {
        return pinyin(text, {
            style: pinyin.STYLE_NORMAL,
            heteronym: false
        })
    }
}