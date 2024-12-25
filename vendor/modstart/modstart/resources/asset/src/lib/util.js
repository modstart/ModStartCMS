const md5 = require('md5');
const sprintf = require('sprintf-js').sprintf;
const base64 = require('js-base64');
const CryptoJS = require("crypto-js");

var Util = {};

/**
 * @Util HTML字符串转义
 * @method MS.util.specialchars
 * @param str string
 * @returns string
 */
Util.specialchars = function (str) {
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
};

/**
 * @Util 纯文本转HTML
 * @method MS.util.text2html
 * @param str string 需要转换的字符串
 * @return string 转换后的字符串
 */
Util.text2html = function (str) {
    str = Util.specialchars(str);
    str = str.replace(/\n/g, '</p><p>');
    return '<p>' + str + '</p>';
};

/**
 * @Util HTML转纯段落文字
 * @method MS.util.text2paragraph
 * @param str string 需要转换的字符串
 * @return string 转换后的字符串
 */
Util.text2paragraph = function (str) {
    str = str.replace(/\n/g, '</p><p>');
    return '<p>' + str + '</p>';
};

/**
 * @Util urlencode编码
 * @method MS.util.urlencode
 * @param str string 需要编码的字符串
 * @return string 编码后的字符串
 */
Util.urlencode = function (str) {
    str = (str + '').toString();
    return encodeURIComponent(str).replace(/!/g, '%21').replace(/'/g, '%27').replace(/\(/g, '%28').replace(/\)/g, '%29').replace(/\*/g, '%2A').replace(/%20/g, '+');
};

/**
 * @Util 获取URL参数
 * @method MS.util.randomString
 * @param len int 长度 默认16
 * @return string 随机字符串
 */
Util.randomString = function (len) {
    len = len || 16;
    var $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    var maxPos = $chars.length;
    var pwd = '';
    for (var i = 0; i < len; i++) {
        pwd += $chars.charAt(Math.floor(Math.random() * maxPos));
    }
    return pwd;
};

/**
 * @Util 获取根window
 * @method MS.util.getRootWindow
 * @return window
 */
Util.getRootWindow = function () {
    var w;
    w = window;
    while (w.self !== w.parent) {
        w = w.parent;
    }
    return w;
};

Util.fixPath = function (path, cdn) {
    cdn = cdn || '';
    if (!path) {
        return '';
    }
    if (path.indexOf('http://') === 0 || path.indexOf('https://') === 0 || path.indexOf('//') === 0) {
        return path;
    }
    if (path.indexOf('/') === 0) {
    } else {
        path = '/' + path;
    }
    if (cdn && (cdn.lastIndexOf('/') == cdn.length - 1)) {
        cdn = cdn.substr(0, cdn.length - 1);
    }
    return cdn + path;
}

Util.fixFullPath = function (path) {
    let cdn = window.location.protocol + '//' + window.location.host + '/'
    return Util.fixPath(path, cdn)
}

Util.objectValue = function (obj, key, value) {
    // console.log('Util.objectValue', key, value)
    if (typeof key == 'string') {
        return Util.objectValue(obj, key.split('.'), value)
    } else if (key.length == 1 && value !== undefined) {
        return obj[key[0]] = value
    } else if (key.length == 0) {
        return obj
    } else {
        if (/^\d+$/.test(key[0])) {
            key[0] = parseInt(key[0])
        }
        return Util.objectValue(obj[key[0]], key.slice(1), value)
    }
}

Util.fullscreen = {
    /**
     * @Util 进入全屏
     * @method MS.util.fullscreen.enter
     * @param callback function 回调函数
     */
    enter: function (callback) {
        var docElm = document.documentElement;
        //W3C
        if (docElm.requestFullscreen) {
            docElm.requestFullscreen();
            setTimeout(function () {
                callback && callback();
            }, 1000);
        }
        //FireFox
        else if (docElm.mozRequestFullScreen) {
            docElm.mozRequestFullScreen();
            setTimeout(function () {
                callback && callback();
            }, 1000);
        }
        //Chrome等
        else if (docElm.webkitRequestFullScreen) {
            docElm.webkitRequestFullScreen();
            setTimeout(function () {
                callback && callback();
            }, 1000);
        }
        //IE11
        else if (elem.msRequestFullscreen) {
            elem.msRequestFullscreen();
            setTimeout(function () {
                callback && callback();
            }, 1000);
        }
    }, /**
     * @Util 退出全屏
     * @method MS.util.fullscreen.exit
     * @param callback function 回调函数
     */
    exit: function (callback) {
        if (document.exitFullscreen) {
            document.exitFullscreen();
            setTimeout(function () {
                callback && callback();
            }, 1000);
        } else if (document.mozCancelFullScreen) {
            document.mozCancelFullScreen();
            setTimeout(function () {
                callback && callback();
            }, 1000);
        } else if (document.webkitCancelFullScreen) {
            document.webkitCancelFullScreen();
            setTimeout(function () {
                callback && callback();
            }, 1000);
        } else if (document.msExitFullscreen) {
            document.msExitFullscreen();
            setTimeout(function () {
                callback && callback();
            }, 1000);
        }
    }, /**
     * @Util 判断是否全屏
     * @method MS.util.fullscreen.isFullScreen
     * @return boolean 是否全屏
     */
    isFullScreen: function () {
        if (document.exitFullscreen) {
            return document.fullscreen;
        } else if (document.mozCancelFullScreen) {
            return document.mozFullScreen;
        } else if (document.webkitCancelFullScreen) {
            return document.webkitIsFullScreen;
        } else if (document.msExitFullscreen) {
            return document.msFullscreenElement;
        }
        return false;
    }, /**
     * @Util 切换全屏
     * @method MS.util.fullscreen.trigger
     * @param callback function 回调函数
     */
    trigger: function (callback) {
        if (Util.fullscreen.isFullScreen()) {
            Util.fullscreen.exit(function () {
                callback && callback('exit');
            });
        } else {
            Util.fullscreen.enter(function () {
                callback && callback('enter');
            });
        }
    }
};

/**
 * @Util 滚动到指定位置
 * @method MS.util.scrollTo
 * @param selector string 选择器
 * @param container string 容器选择器，如果不传则为body
 * @param param object 参数
 */
Util.scrollTo = function (selector, container, param) {
    var opt = Object.assign({
        offset: 0
    }, param)
    var $target = $(selector);
    if (!$target.length) {
        console.warn('Util.scroll target=( ' + selector + ' ) not found');
        return;
    }
    var top = $target.offset().top;
    if (container) {
        var $container = $(container)
        if (!$target.length) {
            console.warn('Util.scroll container=( ' + container + ' ) not found');
            return;
        }
        var containerTop = $container.offset().top
        var containerScrollTop = $container.scrollTop()
        $container.stop().animate({scrollTop: containerScrollTop + top - containerTop + opt.offset}, 200);
    } else {
        $('html,body').stop().animate({scrollTop: top + opt.offset}, 200);
    }
};

/**
 * @Util 动态设置样式
 * @method MS.util.setStyleContent
 * @param id string 样式ID
 * @param css string 样式内容
 */
Util.setStyleContent = function (id, css) {
    let style = document.getElementById(id)
    if (!style) {
        style = document.createElement('style')
        style.type = 'text/css'
        style.id = id
        document.getElementsByTagName('head')[0].appendChild(style)
        style = document.getElementById(id)
    }
    style.innerHTML = css
};
/**
 * @Util 动态加载JS
 * @method MS.util.loadScript
 * @param url string js地址
 * @param cb function 回调函数
 */
Util.loadScript = function (url, cb) {
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
};
/**
 * @Util 动态加载CSS
 * @method MS.util.loadStylesheet
 * @param url string css地址
 * @param cb function 回调函数
 */
Util.loadStylesheet = function (url, cb) {
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
};

/**
 * @Util 计算MD5值
 * @method MS.util.md5
 * @param data string 待计算的字符串
 */
Util.md5 = function (data) {
    return md5(data)
};

/**
 * 框架消息通讯
 */
Util.iframeMessage = {
    queue: [], serve: {}, win: {
        recv: null, send: null,
    }, mount: function (recvWin, sendWin) {
        if (MS.util.iframeMessage.win.recv !== recvWin) {
            recvWin.addEventListener('message', function (e) {
                if (!e.data || !e.data.group || !e.data.id) {
                    return
                }
                for (var i = 0; i < MS.util.iframeMessage.queue.length; i++) {
                    if (MS.util.iframeMessage.queue[i].id === e.data.id) {
                        MS.util.iframeMessage.queue[i].cb(e.data.data)
                        MS.util.iframeMessage.queue.splice(i, 1)
                        return
                    }
                }
                if (!(e.data.group in e.data, MS.util.iframeMessage.serve)) {
                    return
                }
                var data = e.data
                MS.util.iframeMessage.serve[data.group](data.action, data.data, function (result) {
                    MS.util.iframeMessage.safeSend({
                        id: data.id, group: data.group, data: result,
                    })
                });
            }, false)
        }
        MS.util.iframeMessage.win.recv = recvWin
        MS.util.iframeMessage.win.send = sendWin
    }, safeSend(data) {
        if (!MS.util.iframeMessage.win.send) {
            setTimeout(function () {
                MS.util.iframeMessage.safeSend(data)
            }, 100)
            return
        }
        MS.util.iframeMessage.win.send.postMessage(data, '*')
    }, server: function (group, callback) {
        MS.util.iframeMessage.serve[group] = callback
    }, rpc: function (group, action, data, cb) {
        cb = cb || null
        var payload = {
            id: window.MS.util.randomString(10),
            expire: (new Date()).getTime() + 60 * 1000,
            group: group,
            action: action,
            data: data,
            cb: cb,
        }
        if (cb) {
            MS.util.iframeMessage.queue.push(payload)
        }
        MS.util.iframeMessage.win.send.postMessage(JSON.parse(JSON.stringify(payload)), '*')
    }
};

var currentMaxZIndex = -1;
/**
 * @Util 获取页面下一个最大的z-index
 * @method MS.util.getNextMaxZIndex
 * @param forceRefresh boolean 是否强制刷新
 * @return int z-index
 */
Util.getNextMaxZIndex = function (forceRefresh) {
    forceRefresh = forceRefresh || false
    if (forceRefresh || -1 === currentMaxZIndex) {
        $("*").each(function () {
            var zindex = parseInt($(this).css("z-index"));
            if (zindex > 0 && zindex > currentMaxZIndex) {
                currentMaxZIndex = zindex;
            }
        });
    }
    currentMaxZIndex++;
    return currentMaxZIndex;
}

Util.browser = {
    /**
     * @Util 获取浏览器类型
     * @method MS.util.browser.type
     * @return string 浏览器类型
     */
    type: function () {
        var userAgent = navigator.userAgent;
        if (userAgent.indexOf("Opera") > -1) {
            return "Opera"
        }
        if (userAgent.indexOf("Firefox") > -1) {
            return "Firefox"
        }
        if (userAgent.indexOf("Chrome") > -1) {
            return "Chrome"
        }
        if (userAgent.indexOf("Safari") > -1) {
            return "Safari"
        }
        if (userAgent.indexOf("compatible") > -1 && userAgent.indexOf("MSIE") > -1 && !isOpera) {
            return "IE"
        }
    }, /**
     * @Util 判断浏览器类型
     * @method MS.util.browser.is
     * @param type string|array 浏览器类型
     * @return boolean 是否为指定浏览器类型
     * @example
     * MS.util.browser.is('Chrome')
     * MS.util.browser.is(['Firefox', 'Chrome'])
     */
    is: function (type) {
        if (typeof type === 'string') {
            type = [type]
        }
        for (var i = 0; i < type.length; i++) {
            if (type[i] === this.type()) {
                return true
            }
        }
        return false
    }
}

Util.sprintf = sprintf;

Util.base64 = {
    encode: function (data) {
        return base64.Base64.encode(data)
    },
    decode: function (data) {
        return base64.Base64.decode(data)
    },
    fromUint8Array: function (data) {
        return base64.Base64.fromUint8Array(data)
    }
};

Util.encrypt = {
    aesEncode: function (key, data) {
        return CryptoJS.AES.encrypt(data, key).toString();
    },
    aesDecode: function (key, data) {
        return CryptoJS.AES.decrypt(data, key).toString(CryptoJS.enc.Utf8);
    },
    all: CryptoJS
}

module.exports = Util;
