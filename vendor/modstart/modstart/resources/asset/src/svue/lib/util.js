const dateFormat = require('dateformat');
const randomstring = require("randomstring");
const queryString = require('query-string');
const $ = require('jquery');
const md5 = require('md5');
const sprintf = require('sprintf-js').sprintf


export const UUID = {
    // xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx (8-4-4-4-12)
    get: function () {
        var d = new Date()
        var pcs = []
        pcs.push(dateFormat(d, 'yyyymmdd'))
        pcs.push(dateFormat(d, 'hhMM'))
        pcs.push(dateFormat(d, 'ssL'))
        pcs.push(randomstring.generate({
            length: 12,
            charset: 'hex'
        }))
        return pcs.join('-')
    }
}

export const DatetimeFormat = {
    date: 'yyyy-mm-dd',
    time: 'HH:MM:ss',
    datetime: 'yyyy-mm-dd HH:MM:ss',
    now: function () {
        return Date()
    },
    format: function (date, format) {
        return dateFormat(date, format)
    }
}

export const HtmlUtil = {
    specialchars: function (str) {
        var s = [];
        if (!str) {
            return '';
        }
        if (str.length == 0) {
            return '';
        }
        for (var i = 0; i < str.length; i++) {
            switch (str.substr(i, 1)) {
                case "<":
                    s.push("&lt;");
                    break;
                case ">":
                    s.push("&gt;");
                    break;
                case "&":
                    s.push("&amp;");
                    break;
                case " ":
                    s.push("&nbsp;");
                    break;
                case "\"":
                    s.push("&quot;");
                    break;
                default:
                    s.push(str.substr(i, 1));
                    break;
            }
        }
        return s.join('');
    }
}

export const FormatUtil = {
    telephone(number) {
        if (!number) {
            return null
        }
        // console.log('before',number);
        [/\+86/g, /\+/g, / /g, /\(/g, /\)/g, /-/g, /（/g, /）/g, /　/g, /"/g, /;/g, /\t/g].forEach(o => {
            number = number.replace(o, '')
        })
        // console.log('after',number)
        if (/^[0-9]{3,20}$/.test(number)) {
            return number
        }
        return null
    }
}

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

function str2asc(strstr) {
    return ("0" + strstr.charCodeAt(0).toString(16)).slice(-2);
}

function asc2str(ascasc) {
    return String.fromCharCode(ascasc);
}

export const ArrayUtil = {
    unique(arr) {
        if (!arr || !arr.length) {
            return []
        }
        let map = {}
        arr.forEach(o => {
            map[o] = true
        })
        return Object.keys(map)
    }
}

export const UrlUtil = {
    domainUrl(url) {
        url = url || ''
        const base = window.location.protocol + '//' + window.location.host
        if (url) {
            return base + '/' + url
        }
        return base
    },
    urlencode(str) {
        let ret = "";
        const strSpecial = "!\"#$%&'()*+,/:;<=>?[]^`{|}~%";
        let tt = "";
        for (let i = 0; i < str.length; i++) {
            let chr = str.charAt(i);
            let c = str2asc(chr);
            tt += chr + ":" + c + "n";
            if (parseInt("0x" + c) > 0x7f) {
                ret += "%" + c.slice(0, 2) + "%" + c.slice(-2);
            } else {
                if (chr === " ")
                    ret += "+";
                else if (strSpecial.indexOf(chr) !== -1)
                    ret += "%" + c.toString(16);
                else
                    ret += chr;
            }
        }
        return ret;
    },
    urldecode(str) {
        let ret = "";
        str = str + ''
        for (let i = 0; i < str.length; i++) {
            let chr = str.charAt(i);
            if (chr === "+") {
                ret += " ";
            } else if (chr === "%") {
                let asc = str.substring(i + 1, i + 3);
                if (parseInt("0x" + asc) > 0x7f) {
                    ret += asc2str(parseInt("0x" + asc + str.substring(i + 4, i + 6)));
                    i += 5;
                } else {
                    ret += asc2str(parseInt("0x" + asc));
                    i += 2;
                }
            } else {
                ret += chr;
            }
        }
        return ret;
    },
    getQueries(query = undefined) {
        return UrlUtil.parseQuery(query)
    },
    getQuery(key, defaultValue = null, query = undefined) {
        const param = UrlUtil.parseQuery(query)
        if (key in param) {
            return param[key]
        }
        return defaultValue
    },
    parseQuery(str) {
        str = str || window.location.search
        return queryString.parse(str)
    },
    buildQuery(param) {
        return queryString.stringify(param)
    }
}


export const JsonUtil = {
    extend() {
        return $.extend(...arguments)
    },
    clone(obj) {
        return JSON.parse(JSON.stringify(obj))
    },
    equal(o1, o2) {
        return JSON.stringify(o1) === JSON.stringify(o2)
    },
    notEqual(o1, o2) {
        return !JsonUtil.equal(o1, o2)
    },
    clearObject(obj) {
        let type
        for (var i in obj) {
            type = typeof obj[i]
            switch (type) {
                case 'string':
                    obj[i] = ''
                    break;
                case 'number':
                    obj[i] = 0
                    break;
            }
        }
    }
}

export const BeanUtil = {
    /**
     * 使用 valuePool 更新 bean ，valuePool要包含全部字段
     *
     * @param bean
     * @param valuePool
     */
    assign(bean, valuePool) {
        if (!bean || !valuePool) {
            return
        }
        Object.keys(bean).map(o => {
            bean[o] = valuePool[o]
        })
    },
    /**
     * 使用 beanNewValue 更新 bean，beanNewValue可以是部分字段
     * @param bean
     * @param beanNewValue
     */
    update(bean, beanNewValue) {
        if (!bean || !beanNewValue) {
            return
        }
        Object.keys(beanNewValue).map(o => {
            bean[o] = beanNewValue[o]
        })
    },
    /**
     * 判断两个Bean是否相等，注意键值的顺序也要一样
     * @param o1
     * @param o2
     * @returns {boolean}
     */
    equal(o1, o2) {
        return JSON.stringify(o1) === JSON.stringify(o2)
    },
    notEqual(o1, o2) {
        return !BeanUtil.equal(o1, o2)
    },
    clone(obj) {
        return JSON.parse(JSON.stringify(obj))
    },
}


export const UiUtil = {
    treeToKeyBoolean(tree, values, valueKey, childrenKey) {
        valueKey = valueKey || 'name'
        childrenKey = childrenKey || 'children'
        let list = []
        const walk = (node) => {
            node.map(o => {
                list.push(o[valueKey])
                if (o[childrenKey]) {
                    walk(o[childrenKey])
                }
            })
        }
        walk(tree)
        return UiUtil.listToKeyBoolean(list, values)
    },
    listToKeyBoolean(list, values) {
        let keyBooleanMap = {}
        values = values || []
        list.map(o => keyBooleanMap[o] = values.indexOf(o) >= 0)
        return keyBooleanMap
    },
    // 将 {} 中将 key 为 true 的值取出返回 list
    keyBooleanToList(map) {
        let list = []
        Object.keys(map).map(k => {
            if (map[k]) {
                list.push(k)
            }
        })
        return list
    }
}

export const DomUtil = {
    // 动态设置样式
    setStyleContent(id, css) {
        let style = document.getElementById(id)
        if (!style) {
            style = document.createElement('style')
            style.type = 'text/css'
            style.id = id
            document.getElementsByTagName('head')[0].appendChild(style)
            style = document.getElementById(id)
        }
        style.innerHTML = css
    },
    // 动态加载JS
    loadScript(url, cb) {
        let id = 's_' + md5(url)
        let script = document.getElementById(id)
        if (script) {
            cb && cb({isNew: false})
            return
        }
        script = document.createElement('script')
        script.id = id
        script.src = url
        script.onload = () => {
            cb && cb({isNew: true})
        }
        document.getElementsByTagName('head')[0].appendChild(script)
    },
    loadScripts(urls, cb) {
        let loads = {};
        for (let url of urls) {
            loads[url] = null
            DomUtil.loadScript(url, data => {
                loads[url] = data
            })
        }
        let watch = () => {
            for (let o in loads) {
                if (!loads[o]) {
                    setTimeout(() => {
                        watch()
                    }, 100)
                    return
                }
            }
            cb && cb()
        }
        setTimeout(() => {
            watch()
        }, 100)
    }
    ,
    // 动态加载CSS
    loadStylesheet(url, cb) {
        let id = 's_' + md5(url)
        let link = document.getElementById(id)
        if (link) {
            cb && cb({isNew: false})
            return
        }
        link = document.createElement('link')
        link.id = id
        link.rel = 'stylesheet'
        link.type = 'text/css'
        link.href = url
        link.onload = () => {
            cb && cb({isNew: true})
        }
        document.getElementsByTagName('head')[0].appendChild(link)
    }
}

const parser = require('ua-parser-js');
export const AgentUtil = {
    isMobile(ua) {
        ua = ua || window.navigator.userAgent
        const device = parser.setUA(ua).getDevice()
        return device.type === 'mobile'
    },
    isIOS() {
        let u = navigator.userAgent;
        return !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/)
    },
    isWX() {
        let ua = window.navigator.userAgent.toLowerCase();
        return ua.match(/MicroMessenger/i) === 'micromessenger'
    }
}
