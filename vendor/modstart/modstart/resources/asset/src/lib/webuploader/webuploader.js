/* WebUploader 1.0.0 */
!function (a, b) {
    var c, d = {}, e = function (a, b) {
        var c, d, e;
        if ("string" == typeof a) return h(a);
        for (c = [], d = a.length, e = 0; d > e; e++) c.push(h(a[e]));
        return b.apply(null, c)
    }, f = function (a, b, c) {
        2 === arguments.length && (c = b, b = null), e(b || [], function () {
            g(a, c, arguments)
        })
    }, g = function (a, b, c) {
        var f, g = {exports: b};
        "function" == typeof b && (c.length || (c = [e, g.exports, g]), f = b.apply(null, c), void 0 !== f && (g.exports = f)), d[a] = g.exports
    }, h = function (b) {
        var c = d[b] || a[b];
        if (!c) throw new Error("`" + b + "` is undefined");
        return c
    }, i = function (a) {
        var b, c, e, f, g, h;
        h = function (a) {
            return a && a.charAt(0).toUpperCase() + a.substr(1)
        };
        for (b in d) if (c = a, d.hasOwnProperty(b)) {
            for (e = b.split("/"), g = h(e.pop()); f = h(e.shift());) c[f] = c[f] || {}, c = c[f];
            c[g] = d[b]
        }
        return a
    }, j = function (c) {
        return a.__dollar = c, i(b(a, f, e))
    };
    "object" == typeof module && "object" == typeof module.exports ? module.exports = j() : "function" == typeof define && define.amd ? define(["jquery"], j) : (c = a.WebUploader, a.WebUploader = j(), a.WebUploader.noConflict = function () {
        a.WebUploader = c
    })
}(window, function (a, b, c) {
    return b("dollar-third", [], function () {
        var b = a.require, c = a.__dollar || a.jQuery || a.Zepto || b("jquery") || b("zepto");
        if (!c) throw new Error("jQuery or Zepto not found!");
        return c
    }), b("dollar", ["dollar-third"], function (a) {
        return a
    }), b("promise-third", ["dollar"], function (a) {
        return {
            Deferred: a.Deferred, when: a.when, isPromise: function (a) {
                return a && "function" == typeof a.then
            }
        }
    }), b("promise", ["promise-third"], function (a) {
        return a
    }), b("base", ["dollar", "promise"], function (b, c) {
        function d(a) {
            return function () {
                return h.apply(a, arguments)
            }
        }

        function e(a, b) {
            return function () {
                return a.apply(b, arguments)
            }
        }

        function f(a) {
            var b;
            return Object.create ? Object.create(a) : (b = function () {
            }, b.prototype = a, new b)
        }

        var g = function () {
        }, h = Function.call;
        return {
            version: "1.0.0", $: b, Deferred: c.Deferred, isPromise: c.isPromise, when: c.when, browser: function (a) {
                var b = {}, c = a.match(/WebKit\/([\d.]+)/),
                    d = a.match(/Chrome\/([\d.]+)/) || a.match(/CriOS\/([\d.]+)/),
                    e = a.match(/MSIE\s([\d\.]+)/) || a.match(/(?:trident)(?:.*rv:([\w.]+))?/i),
                    f = a.match(/Firefox\/([\d.]+)/), g = a.match(/Safari\/([\d.]+)/), h = a.match(/OPR\/([\d.]+)/);
                return c && (b.webkit = parseFloat(c[1])), d && (b.chrome = parseFloat(d[1])), e && (b.ie = parseFloat(e[1])), f && (b.firefox = parseFloat(f[1])), g && (b.safari = parseFloat(g[1])), h && (b.opera = parseFloat(h[1])), b
            }(navigator.userAgent), os: function (a) {
                var b = {}, c = a.match(/(?:Android);?[\s\/]+([\d.]+)?/),
                    d = a.match(/(?:iPad|iPod|iPhone).*OS\s([\d_]+)/);
                return c && (b.android = parseFloat(c[1])), d && (b.ios = parseFloat(d[1].replace(/_/g, "."))), b
            }(navigator.userAgent), inherits: function (a, c, d) {
                var e;
                return "function" == typeof c ? (e = c, c = null) : e = c && c.hasOwnProperty("constructor") ? c.constructor : function () {
                    return a.apply(this, arguments)
                }, b.extend(!0, e, a, d || {}), e.__super__ = a.prototype, e.prototype = f(a.prototype), c && b.extend(!0, e.prototype, c), e
            }, noop: g, bindFn: e, log: function () {
                return a.console ? e(console.log, console) : g
            }(), nextTick: function () {
                return function (a) {
                    setTimeout(a, 1)
                }
            }(), slice: d([].slice), guid: function () {
                var a = 0;
                return function (b) {
                    for (var c = (+new Date).toString(32), d = 0; 5 > d; d++) c += Math.floor(65535 * Math.random()).toString(32);
                    return (b || "wu_") + c + (a++).toString(32)
                }
            }(), formatSize: function (a, b, c) {
                var d;
                for (c = c || ["B", "K", "M", "G", "TB"]; (d = c.shift()) && a > 1024;) a /= 1024;
                return ("B" === d ? a : a.toFixed(b || 2)) + d
            }
        }
    }), b("mediator", ["base"], function (a) {
        function b(a, b, c, d) {
            return f.grep(a, function (a) {
                return !(!a || b && a.e !== b || c && a.cb !== c && a.cb._cb !== c || d && a.ctx !== d)
            })
        }

        function c(a, b, c) {
            f.each((a || "").split(h), function (a, d) {
                c(d, b)
            })
        }

        function d(a, b) {
            for (var c, d = !1, e = -1, f = a.length; ++e < f;) if (c = a[e], c.cb.apply(c.ctx2, b) === !1) {
                d = !0;
                break
            }
            return !d
        }

        var e, f = a.$, g = [].slice, h = /\s+/;
        return e = {
            on: function (a, b, d) {
                var e, f = this;
                return b ? (e = this._events || (this._events = []), c(a, b, function (a, b) {
                    var c = {e: a};
                    c.cb = b, c.ctx = d, c.ctx2 = d || f, c.id = e.length, e.push(c)
                }), this) : this
            }, once: function (a, b, d) {
                var e = this;
                return b ? (c(a, b, function (a, b) {
                    var c = function () {
                        return e.off(a, c), b.apply(d || e, arguments)
                    };
                    c._cb = b, e.on(a, c, d)
                }), e) : e
            }, off: function (a, d, e) {
                var g = this._events;
                return g ? a || d || e ? (c(a, d, function (a, c) {
                    f.each(b(g, a, c, e), function () {
                        delete g[this.id]
                    })
                }), this) : (this._events = [], this) : this
            }, trigger: function (a) {
                var c, e, f;
                return this._events && a ? (c = g.call(arguments, 1), e = b(this._events, a), f = b(this._events, "all"), d(e, c) && d(f, arguments)) : this
            }
        }, f.extend({
            installTo: function (a) {
                return f.extend(a, e)
            }
        }, e)
    }), b("uploader", ["base", "mediator"], function (a, b) {
        function c(a) {
            this.options = d.extend(!0, {}, c.options, a), this._init(this.options)
        }

        var d = a.$;
        return c.options = {debug: !1}, b.installTo(c.prototype), d.each({
            upload: "start-upload",
            stop: "stop-upload",
            getFile: "get-file",
            getFiles: "get-files",
            addFile: "add-file",
            addFiles: "add-file",
            sort: "sort-files",
            removeFile: "remove-file",
            cancelFile: "cancel-file",
            skipFile: "skip-file",
            retry: "retry",
            isInProgress: "is-in-progress",
            makeThumb: "make-thumb",
            md5File: "md5-file",
            getDimension: "get-dimension",
            addButton: "add-btn",
            predictRuntimeType: "predict-runtime-type",
            refresh: "refresh",
            disable: "disable",
            enable: "enable",
            reset: "reset"
        }, function (a, b) {
            c.prototype[a] = function () {
                return this.request(b, arguments)
            }
        }), d.extend(c.prototype, {
            state: "pending", _init: function (a) {
                var b = this;
                b.request("init", a, function () {
                    b.state = "ready", b.trigger("ready")
                })
            }, option: function (a, b) {
                var c = this.options;
                return arguments.length > 1 ? (d.isPlainObject(b) && d.isPlainObject(c[a]) ? d.extend(c[a], b) : c[a] = b, void 0) : a ? c[a] : c
            }, getStats: function () {
                var a = this.request("get-stats");
                return a ? {
                    successNum: a.numOfSuccess,
                    progressNum: a.numOfProgress,
                    cancelNum: a.numOfCancel,
                    invalidNum: a.numOfInvalid,
                    uploadFailNum: a.numOfUploadFailed,
                    queueNum: a.numOfQueue,
                    interruptNum: a.numOfInterrupt
                } : {}
            }, trigger: function (a) {
                var c = [].slice.call(arguments, 1), e = this.options,
                    f = "on" + a.substring(0, 1).toUpperCase() + a.substring(1);
                return b.trigger.apply(this, arguments) === !1 || d.isFunction(e[f]) && e[f].apply(this, c) === !1 || d.isFunction(this[f]) && this[f].apply(this, c) === !1 || b.trigger.apply(b, [this, a].concat(c)) === !1 ? !1 : !0
            }, destroy: function () {
                this.request("destroy", arguments), this.off()
            }, request: a.noop
        }), a.create = c.create = function (a) {
            return new c(a)
        }, a.Uploader = c, c
    }), b("runtime/runtime", ["base", "mediator"], function (a, b) {
        function c(b) {
            this.options = d.extend({container: document.body}, b), this.uid = a.guid("rt_")
        }

        var d = a.$, e = {}, f = function (a) {
            for (var b in a) if (a.hasOwnProperty(b)) return b;
            return null
        };
        return d.extend(c.prototype, {
            getContainer: function () {
                var a, b, c = this.options;
                return this._container ? this._container : (a = d(c.container || document.body), b = d(document.createElement("div")), b.attr("id", "rt_" + this.uid), b.css({
                    position: "absolute",
                    top: "0px",
                    left: "0px",
                    width: "1px",
                    height: "1px",
                    overflow: "hidden"
                }), a.append(b), a.addClass("webuploader-container"), this._container = b, this._parent = a, b)
            }, init: a.noop, exec: a.noop, destroy: function () {
                this._container && this._container.remove(), this._parent && this._parent.removeClass("webuploader-container"), this.off()
            }
        }), c.orders = "html5,flash", c.addRuntime = function (a, b) {
            e[a] = b
        }, c.hasRuntime = function (a) {
            return !!(a ? e[a] : f(e))
        }, c.create = function (a, b) {
            var g, h;
            if (b = b || c.orders, d.each(b.split(/\s*,\s*/g), function () {
                return e[this] ? (g = this, !1) : void 0
            }), g = g || f(e), !g) throw new Error("Runtime Error");
            return h = new e[g](a)
        }, b.installTo(c.prototype), c
    }), b("runtime/client", ["base", "mediator", "runtime/runtime"], function (a, b, c) {
        function d(b, d) {
            var f, g = a.Deferred();
            this.uid = a.guid("client_"), this.runtimeReady = function (a) {
                return g.done(a)
            }, this.connectRuntime = function (b, h) {
                if (f) throw new Error("already connected!");
                return g.done(h), "string" == typeof b && e.get(b) && (f = e.get(b)), f = f || e.get(null, d), f ? (a.$.extend(f.options, b), f.__promise.then(g.resolve), f.__client++) : (f = c.create(b, b.runtimeOrder), f.__promise = g.promise(), f.once("ready", g.resolve), f.init(), e.add(f), f.__client = 1), d && (f.__standalone = d), f
            }, this.getRuntime = function () {
                return f
            }, this.disconnectRuntime = function () {
                f && (f.__client--, f.__client <= 0 && (e.remove(f), delete f.__promise, f.destroy()), f = null)
            }, this.exec = function () {
                if (f) {
                    var c = a.slice(arguments);
                    return b && c.unshift(b), f.exec.apply(this, c)
                }
            }, this.getRuid = function () {
                return f && f.uid
            }, this.destroy = function (a) {
                return function () {
                    a && a.apply(this, arguments), this.trigger("destroy"), this.off(), this.exec("destroy"), this.disconnectRuntime()
                }
            }(this.destroy)
        }

        var e;
        return e = function () {
            var a = {};
            return {
                add: function (b) {
                    a[b.uid] = b
                }, get: function (b, c) {
                    var d;
                    if (b) return a[b];
                    for (d in a) if (!c || !a[d].__standalone) return a[d];
                    return null
                }, remove: function (b) {
                    delete a[b.uid]
                }
            }
        }(), b.installTo(d.prototype), d
    }), b("lib/dnd", ["base", "mediator", "runtime/client"], function (a, b, c) {
        function d(a) {
            a = this.options = e.extend({}, d.options, a), a.container = e(a.container), a.container.length && c.call(this, "DragAndDrop")
        }

        var e = a.$;
        return d.options = {accept: null, disableGlobalDnd: !1}, a.inherits(c, {
            constructor: d, init: function () {
                var a = this;
                a.connectRuntime(a.options, function () {
                    a.exec("init"), a.trigger("ready")
                })
            }
        }), b.installTo(d.prototype), d
    }), b("widgets/widget", ["base", "uploader"], function (a, b) {
        function c(a) {
            if (!a) return !1;
            var b = a.length, c = e.type(a);
            return 1 === a.nodeType && b ? !0 : "array" === c || "function" !== c && "string" !== c && (0 === b || "number" == typeof b && b > 0 && b - 1 in a)
        }

        function d(a) {
            this.owner = a, this.options = a.options
        }

        var e = a.$, f = b.prototype._init, g = b.prototype.destroy, h = {}, i = [];
        return e.extend(d.prototype, {
            init: a.noop, invoke: function (a, b) {
                var c = this.responseMap;
                return c && a in c && c[a] in this && e.isFunction(this[c[a]]) ? this[c[a]].apply(this, b) : h
            }, request: function () {
                return this.owner.request.apply(this.owner, arguments)
            }
        }), e.extend(b.prototype, {
            _init: function () {
                var a = this, b = a._widgets = [], c = a.options.disableWidgets || "";
                return e.each(i, function (d, e) {
                    (!c || !~c.indexOf(e._name)) && b.push(new e(a))
                }), f.apply(a, arguments)
            }, request: function (b, d, e) {
                var f, g, i, j, k = 0, l = this._widgets, m = l && l.length, n = [], o = [];
                for (d = c(d) ? d : [d]; m > k; k++) f = l[k], g = f.invoke(b, d), g !== h && (a.isPromise(g) ? o.push(g) : n.push(g));
                return e || o.length ? (i = a.when.apply(a, o), j = i.pipe ? "pipe" : "then", i[j](function () {
                    var b = a.Deferred(), c = arguments;
                    return 1 === c.length && (c = c[0]), setTimeout(function () {
                        b.resolve(c)
                    }, 1), b.promise()
                })[e ? j : "done"](e || a.noop)) : n[0]
            }, destroy: function () {
                g.apply(this, arguments), this._widgets = null
            }
        }), b.register = d.register = function (b, c) {
            var f, g = {init: "init", destroy: "destroy", name: "anonymous"};
            return 1 === arguments.length ? (c = b, e.each(c, function (a) {
                return "_" === a[0] || "name" === a ? ("name" === a && (g.name = c.name), void 0) : (g[a.replace(/[A-Z]/g, "-$&").toLowerCase()] = a, void 0)
            })) : g = e.extend(g, b), c.responseMap = g, f = a.inherits(d, c), f._name = g.name, i.push(f), f
        }, b.unRegister = d.unRegister = function (a) {
            if (a && "anonymous" !== a) for (var b = i.length; b--;) i[b]._name === a && i.splice(b, 1)
        }, d
    }), b("widgets/filednd", ["base", "uploader", "lib/dnd", "widgets/widget"], function (a, b, c) {
        var d = a.$;
        return b.options.dnd = "", b.register({
            name: "dnd", init: function (b) {
                if (b.dnd && "html5" === this.request("predict-runtime-type")) {
                    var e, f = this, g = a.Deferred(),
                        h = d.extend({}, {disableGlobalDnd: b.disableGlobalDnd, container: b.dnd, accept: b.accept});
                    return this.dnd = e = new c(h), e.once("ready", g.resolve), e.on("drop", function (a) {
                        f.request("add-file", [a])
                    }), e.on("accept", function (a) {
                        return f.owner.trigger("dndAccept", a)
                    }), e.init(), g.promise()
                }
            }, destroy: function () {
                this.dnd && this.dnd.destroy()
            }
        })
    }), b("lib/filepaste", ["base", "mediator", "runtime/client"], function (a, b, c) {
        function d(a) {
            a = this.options = e.extend({}, a), a.container = e(a.container || document.body), c.call(this, "FilePaste")
        }

        var e = a.$;
        return a.inherits(c, {
            constructor: d, init: function () {
                var a = this;
                a.connectRuntime(a.options, function () {
                    a.exec("init"), a.trigger("ready")
                })
            }
        }), b.installTo(d.prototype), d
    }), b("widgets/filepaste", ["base", "uploader", "lib/filepaste", "widgets/widget"], function (a, b, c) {
        var d = a.$;
        return b.register({
            name: "paste", init: function (b) {
                if (b.paste && "html5" === this.request("predict-runtime-type")) {
                    var e, f = this, g = a.Deferred(), h = d.extend({}, {container: b.paste, accept: b.accept});
                    return this.paste = e = new c(h), e.once("ready", g.resolve), e.on("paste", function (a) {
                        f.owner.request("add-file", [a])
                    }), e.init(), g.promise()
                }
            }, destroy: function () {
                this.paste && this.paste.destroy()
            }
        })
    }), b("lib/blob", ["base", "runtime/client"], function (a, b) {
        function c(a, c) {
            var d = this;
            d.source = c, d.ruid = a, this.size = c.size || 0, this.type = !c.type && this.ext && ~"jpg,jpeg,png,gif,bmp".indexOf(this.ext) ? "image/" + ("jpg" === this.ext ? "jpeg" : this.ext) : c.type || "application/octet-stream", b.call(d, "Blob"), this.uid = c.uid || this.uid, a && d.connectRuntime(a)
        }

        return a.inherits(b, {
            constructor: c, slice: function (a, b) {
                return this.exec("slice", a, b)
            }, getSource: function () {
                return this.source
            }
        }), c
    }), b("lib/file", ["base", "lib/blob"], function (a, b) {
        function c(a, c) {
            var f;
            this.name = c.name || "untitled" + d++, f = e.exec(c.name) ? RegExp.$1.toLowerCase() : "", !f && c.type && (f = /\/(jpg|jpeg|png|gif|bmp)$/i.exec(c.type) ? RegExp.$1.toLowerCase() : "", this.name += "." + f), this.ext = f, this.lastModifiedDate = c.lastModifiedDate || c.lastModified && new Date(c.lastModified).toLocaleString() || (new Date).toLocaleString(), b.apply(this, arguments)
        }

        var d = 1, e = /\.([^.]+)$/;
        return a.inherits(b, c)
    }), b("lib/filepicker", ["base", "runtime/client", "lib/file"], function (b, c, d) {
        function e(a) {
            if (a = this.options = f.extend({}, e.options, a), a.container = f(a.id), !a.container.length) throw new Error("按钮指定错误");
            a.innerHTML = a.innerHTML || a.label || a.container.html() || "", a.button = f(a.button || document.createElement("div")), a.button.html(a.innerHTML), a.container.html(a.button), c.call(this, "FilePicker", !0)
        }

        var f = b.$;
        return e.options = {
            button: null,
            container: null,
            label: null,
            innerHTML: null,
            multiple: !0,
            accept: null,
            name: "file",
            style: "webuploader-pick"
        }, b.inherits(c, {
            constructor: e, init: function () {
                var c = this, e = c.options, g = e.button, h = e.style;
                h && g.addClass("webuploader-pick"), c.on("all", function (a) {
                    var b;
                    switch (a) {
                        case"mouseenter":
                            h && g.addClass("webuploader-pick-hover");
                            break;
                        case"mouseleave":
                            h && g.removeClass("webuploader-pick-hover");
                            break;
                        case"change":
                            b = c.exec("getFiles"), c.trigger("select", f.map(b, function (a) {
                                return a = new d(c.getRuid(), a), a._refer = e.container, a
                            }), e.container)
                    }
                }), c.connectRuntime(e, function () {
                    c.refresh(), c.exec("init", e), c.trigger("ready")
                }), this._resizeHandler = b.bindFn(this.refresh, this), f(a).on("resize", this._resizeHandler)
            }, refresh: function () {
                var a = this.getRuntime().getContainer(), b = this.options.button,
                    c = b[0] && b[0].offsetWidth || b.outerWidth() || b.width(),
                    d = b[0] && b[0].offsetHeight || b.outerHeight() || b.height(), e = b.offset();
                c && d && a.css({bottom: "auto", right: "auto", width: c + "px", height: d + "px"}).offset(e)
            }, enable: function () {
                var a = this.options.button;
                a.removeClass("webuploader-pick-disable"), this.refresh()
            }, disable: function () {
                var a = this.options.button;
                this.getRuntime().getContainer().css({top: "-99999px"}), a.addClass("webuploader-pick-disable")
            }, destroy: function () {
                var b = this.options.button;
                f(a).off("resize", this._resizeHandler), b.removeClass("webuploader-pick-disable webuploader-pick-hover webuploader-pick")
            }
        }), e
    }), b("widgets/filepicker", ["base", "uploader", "lib/filepicker", "widgets/widget"], function (a, b, c) {
        var d = a.$;
        return d.extend(b.options, {pick: null, accept: null}), b.register({
            name: "picker", init: function (a) {
                return this.pickers = [], a.pick && this.addBtn(a.pick)
            }, refresh: function () {
                d.each(this.pickers, function () {
                    this.refresh()
                })
            }, addBtn: function (b) {
                var e = this, f = e.options, g = f.accept, h = [];
                if (b) return d.isPlainObject(b) || (b = {id: b}), d(b.id).each(function () {
                    var i, j, k;
                    k = a.Deferred(), i = d.extend({}, b, {
                        accept: d.isPlainObject(g) ? [g] : g,
                        swf: f.swf,
                        runtimeOrder: f.runtimeOrder,
                        id: this
                    }), j = new c(i), j.once("ready", k.resolve), j.on("select", function (a) {
                        e.owner.request("add-file", [a])
                    }), j.on("dialogopen", function () {
                        e.owner.trigger("dialogOpen", j.button)
                    }), j.init(), e.pickers.push(j), h.push(k.promise())
                }), a.when.apply(a, h)
            }, disable: function () {
                d.each(this.pickers, function () {
                    this.disable()
                })
            }, enable: function () {
                d.each(this.pickers, function () {
                    this.enable()
                })
            }, destroy: function () {
                d.each(this.pickers, function () {
                    this.destroy()
                }), this.pickers = null
            }
        })
    }), b("lib/image", ["base", "runtime/client", "lib/blob"], function (a, b, c) {
        function d(a) {
            this.options = e.extend({}, d.options, a), b.call(this, "Image"), this.on("load", function () {
                this._info = this.exec("info"), this._meta = this.exec("meta")
            })
        }

        var e = a.$;
        return d.options = {
            quality: 90,
            crop: !1,
            preserveHeaders: !1,
            allowMagnify: !1
        }, a.inherits(b, {
            constructor: d, info: function (a) {
                return a ? (this._info = a, this) : this._info
            }, meta: function (a) {
                return a ? (this._meta = a, this) : this._meta
            }, loadFromBlob: function (a) {
                var b = this, c = a.getRuid();
                this.connectRuntime(c, function () {
                    b.exec("init", b.options), b.exec("loadFromBlob", a)
                })
            }, resize: function () {
                var b = a.slice(arguments);
                return this.exec.apply(this, ["resize"].concat(b))
            }, crop: function () {
                var b = a.slice(arguments);
                return this.exec.apply(this, ["crop"].concat(b))
            }, getAsDataUrl: function (a) {
                return this.exec("getAsDataUrl", a)
            }, getAsBlob: function (a) {
                var b = this.exec("getAsBlob", a);
                return new c(this.getRuid(), b)
            }
        }), d
    }), b("widgets/image", ["base", "uploader", "lib/image", "widgets/widget"], function (b, c, d) {
        var e = null;
        !function () {
            function b(a) {
                return (b = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (a) {
                    return typeof a
                } : function (a) {
                    return a && "function" == typeof Symbol && a.constructor === Symbol && a !== Symbol.prototype ? "symbol" : typeof a
                })(a)
            }

            function c(a, b) {
                var c = Object.keys(a);
                if (Object.getOwnPropertySymbols) {
                    var d = Object.getOwnPropertySymbols(a);
                    b && (d = d.filter(function (b) {
                        return Object.getOwnPropertyDescriptor(a, b).enumerable
                    })), c.push.apply(c, d)
                }
                return c
            }

            function f(a) {
                for (var b = 1; b < arguments.length; b++) {
                    var d = null != arguments[b] ? arguments[b] : {};
                    b % 2 ? c(Object(d), !0).forEach(function (b) {
                        m(a, b, d[b])
                    }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(a, Object.getOwnPropertyDescriptors(d)) : c(Object(d)).forEach(function (b) {
                        Object.defineProperty(a, b, Object.getOwnPropertyDescriptor(d, b))
                    })
                }
                return a
            }

            function g(a, b) {
                return l(a) || k(a, b) || i(a, b) || h()
            }

            function h() {
                throw TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")
            }

            function i(a, b) {
                if (a) {
                    if ("string" == typeof a) return j(a, b);
                    var c = Object.prototype.toString.call(a).slice(8, -1);
                    if ("Object" === c && a.constructor && (c = a.constructor.name), "Map" === c || "Set" === c) return Array.from(a);
                    if ("Arguments" === c || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(c)) return j(a, b)
                }
            }

            function j(a, b) {
                (null == b || b > a.length) && (b = a.length);
                for (var c = 0, d = Array(b); b > c; c++) d[c] = a[c];
                return d
            }

            function k(a, b) {
                var c = null == a ? null : "undefined" != typeof Symbol && a[Symbol.iterator] || a["@@iterator"];
                if (null != c) {
                    var d, e, f, g, h = [], i = !0, j = !1;
                    try {
                        if (f = (c = c.call(a)).next, 0 === b) {
                            if (Object(c) !== c) return;
                            i = !1
                        } else for (; !(i = (d = f.call(c)).done) && (h.push(d.value), h.length !== b); i = !0) ;
                    } catch (k) {
                        j = !0, e = k
                    } finally {
                        try {
                            if (!i && null != c.return && (g = c.return(), Object(g) !== g)) return
                        } finally {
                            if (j) throw e
                        }
                    }
                    return h
                }
            }

            function l(a) {
                return Array.isArray(a) ? a : void 0
            }

            function m(a, b, c) {
                return (b = n(b)) in a ? Object.defineProperty(a, b, {
                    value: c,
                    enumerable: !0,
                    configurable: !0,
                    writable: !0
                }) : a[b] = c, a
            }

            function n(a) {
                var c = o(a, "string");
                return "symbol" === b(c) ? c : String(c)
            }

            function o(a, c) {
                if ("object" !== b(a) || null === a) return a;
                var d = a[Symbol.toPrimitive];
                if (void 0 !== d) {
                    var e = d.call(a, c || "default");
                    if ("object" !== b(e)) return e;
                    throw TypeError("@@toPrimitive must return a primitive value.")
                }
                return ("string" === c ? String : Number)(a)
            }

            var p;
            e = (p = function () {
                function b(a, b) {
                    return new Promise(function (c, d) {
                        var e;
                        return F(a).then(function (a) {
                            try {
                                return e = a, c(new Blob([b.slice(0, 2), e, b.slice(2)], {type: "image/jpeg"}))
                            } catch (f) {
                                return d(f)
                            }
                        }, d)
                    })
                }

                function c(a, b) {
                    var c = arguments.length > 2 && void 0 !== arguments[2] ? arguments[2] : Date.now();
                    return new Promise(function (d) {
                        for (var e = a.split(","), f = e[0].match(/:(.*?);/)[1], g = globalThis.atob(e[1]), h = g.length, i = new Uint8Array(h); h--;) i[h] = g.charCodeAt(h);
                        var j = new Blob([i], {type: f});
                        j.name = b, j.lastModified = c, d(j)
                    })
                }

                function e(a) {
                    return new Promise(function (b, c) {
                        var d = new P;
                        d.onload = function () {
                            return b(d.result)
                        }, d.onerror = function (a) {
                            return c(a)
                        }, d.readAsDataURL(a)
                    })
                }

                function h(a) {
                    return new Promise(function (b, c) {
                        var e = new d;
                        e.onload = function () {
                            return b(e)
                        }, e.onerror = function (a) {
                            return c(a)
                        }, e.src = a
                    })
                }

                function i() {
                    if (void 0 !== i.cachedResult) return i.cachedResult;
                    var a = K.ETC, b = navigator.userAgent;
                    return /Chrom(e|ium)/i.test(b) ? a = K.CHROME : /iP(ad|od|hone)/i.test(b) && /WebKit/i.test(b) ? a = K.IOS : /Safari/i.test(b) ? a = K.DESKTOP_SAFARI : /Firefox/i.test(b) ? a = K.FIREFOX : (/MSIE/i.test(b) || 1 == !!document.documentMode) && (a = K.IE), i.cachedResult = a, i.cachedResult
                }

                function j(a, b) {
                    for (var c = L[i()], d = a, e = b, f = d * e, g = d > e ? e / d : d / e; f > c * c;) {
                        var h = (c + d) / 2, j = (c + e) / 2;
                        j > h ? (e = j, d = j * g) : (e = h * g, d = h), f = d * e
                    }
                    return {width: d, height: e}
                }

                function k(a, b) {
                    var c, d;
                    try {
                        if (d = (c = new OffscreenCanvas(a, b)).getContext("2d"), null === d) throw Error("getContext of OffscreenCanvas returns null")
                    } catch (e) {
                        d = (c = document.createElement("canvas")).getContext("2d")
                    }
                    return c.width = a, c.height = b, [c, d]
                }

                function l(a, b) {
                    var c = j(a.width, a.height), d = k(c.width, c.height), e = g(d, 2), f = e[0], h = e[1];
                    return b && /jpe?g/.test(b) && (h.fillStyle = "white", h.fillRect(0, 0, f.width, f.height)), h.drawImage(a, 0, 0, f.width, f.height), f
                }

                function n() {
                    return void 0 !== n.cachedResult || (n.cachedResult = ["iPad Simulator", "iPhone Simulator", "iPod Simulator", "iPad", "iPhone", "iPod"].includes(navigator.platform) || navigator.userAgent.includes("Mac") && "undefined" != typeof document && "ontouchend" in document), n.cachedResult
                }

                function o(a) {
                    var b = arguments.length > 1 && void 0 !== arguments[1] ? arguments[1] : {};
                    return new Promise(function (c, d) {
                        var f, g, j = function () {
                            try {
                                return g = l(f, b.fileType || a.type), c([f, g])
                            } catch (e) {
                                return d(e)
                            }
                        }, k = function () {
                            try {
                                var b, c = function (a) {
                                    try {
                                        throw a
                                    } catch (b) {
                                        return d(b)
                                    }
                                };
                                try {
                                    return e(a).then(function (a) {
                                        try {
                                            return b = a, h(b).then(function (a) {
                                                try {
                                                    return f = a, function () {
                                                        try {
                                                            return j()
                                                        } catch (a) {
                                                            return d(a)
                                                        }
                                                    }()
                                                } catch (b) {
                                                    return c(b)
                                                }
                                            }, c)
                                        } catch (e) {
                                            return c(e)
                                        }
                                    }, c)
                                } catch (g) {
                                    c(g)
                                }
                            } catch (i) {
                                return d(i)
                            }
                        };
                        try {
                            if (n() || [K.DESKTOP_SAFARI, K.MOBILE_SAFARI].includes(i())) throw Error("Skip createImageBitmap on IOS and Safari");
                            return createImageBitmap(a).then(function (a) {
                                try {
                                    return f = a, j()
                                } catch (b) {
                                    return k()
                                }
                            }, k)
                        } catch (m) {
                            k()
                        }
                    })
                }

                function p(a, b, d, e) {
                    var f = arguments.length > 4 && void 0 !== arguments[4] ? arguments[4] : 1;
                    return new Promise(function (g, h) {
                        function i() {
                            return g(k)
                        }

                        if ("image/png" === b) return n = (l = (m = a.getContext("2d")).getImageData(0, 0, a.width, a.height)).data, o = I.encode([n.buffer], a.width, a.height, 4096 * f), (k = new Blob([o], {type: b})).name = d, k.lastModified = e, i.call(this);
                        var j = function () {
                            return i.call(this)
                        };
                        if ("image/bmp" === b) return new Promise(function (b) {
                            return J.toBlob(a, b)
                        }).then(function (a) {
                            try {
                                return (k = a).name = d, k.lastModified = e, j.call(this)
                            } catch (b) {
                                return h(b)
                            }
                        }.bind(this), h);
                        var k, l, m, n, o, p, q = function () {
                            return j.call(this)
                        };
                        return "function" == typeof OffscreenCanvas && a instanceof OffscreenCanvas ? a.convertToBlob({
                            type: b,
                            quality: f
                        }).then(function (a) {
                            try {
                                return (k = a).name = d, k.lastModified = e, q.call(this)
                            } catch (b) {
                                return h(b)
                            }
                        }.bind(this), h) : c(p = a.toDataURL(b, f), d, e).then(function (a) {
                            try {
                                return k = a, q.call(this)
                            } catch (b) {
                                return h(b)
                            }
                        }.bind(this), h)
                    })
                }

                function q(a) {
                    a.width = 0, a.height = 0
                }

                function r() {
                    return new Promise(function (a, b) {
                        var d, e, f, g, h;
                        return void 0 !== r.cachedResult ? a(r.cachedResult) : (d = "data:image/jpeg;base64,/9j/4QAiRXhpZgAATU0AKgAAAAgAAQESAAMAAAABAAYAAAAAAAD/2wCEAAEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAf/AABEIAAEAAgMBEQACEQEDEQH/xABKAAEAAAAAAAAAAAAAAAAAAAALEAEAAAAAAAAAAAAAAAAAAAAAAQEAAAAAAAAAAAAAAAAAAAAAEQEAAAAAAAAAAAAAAAAAAAAA/9oADAMBAAIRAxEAPwA/8H//2Q==", c("data:image/jpeg;base64,/9j/4QAiRXhpZgAATU0AKgAAAAgAAQESAAMAAAABAAYAAAAAAAD/2wCEAAEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAf/AABEIAAEAAgMBEQACEQEDEQH/xABKAAEAAAAAAAAAAAAAAAAAAAALEAEAAAAAAAAAAAAAAAAAAAAAAQEAAAAAAAAAAAAAAAAAAAAAEQEAAAAAAAAAAAAAAAAAAAAA/9oADAMBAAIRAxEAPwA/8H//2Q==", "test.jpg", Date.now()).then(function (c) {
                            try {
                                return e = c, o(e).then(function (c) {
                                    try {
                                        return f = c[1], p(f, e.type, e.name, e.lastModified).then(function (c) {
                                            try {
                                                return g = c, q(f), o(g).then(function (c) {
                                                    try {
                                                        return h = c[0], r.cachedResult = 1 === h.width && 2 === h.height, a(r.cachedResult)
                                                    } catch (d) {
                                                        return b(d)
                                                    }
                                                }, b)
                                            } catch (d) {
                                                return b(d)
                                            }
                                        }, b)
                                    } catch (d) {
                                        return b(d)
                                    }
                                }, b)
                            } catch (d) {
                                return b(d)
                            }
                        }, b))
                    })
                }

                function s(a) {
                    return new Promise(function (b, c) {
                        var d = new P;
                        d.onload = function (a) {
                            var c = new DataView(a.target.result);
                            if (65496 != c.getUint16(0, !1)) return b(-2);
                            for (var d = c.byteLength, e = 2; d > e && !(8 >= c.getUint16(e + 2, !1));) {
                                var f = c.getUint16(e, !1);
                                if (e += 2, 65505 == f) {
                                    if (1165519206 != c.getUint32(e += 2, !1)) return b(-1);
                                    var g = 18761 == c.getUint16(e += 6, !1);
                                    e += c.getUint32(e + 4, g);
                                    var h = c.getUint16(e, g);
                                    e += 2;
                                    for (var i = 0; h > i; i++) if (274 == c.getUint16(e + 12 * i, g)) return b(c.getUint16(e + 12 * i + 8, g))
                                } else {
                                    if (65280 != (65280 & f)) break;
                                    e += c.getUint16(e, !1)
                                }
                            }
                            return b(-1)
                        }, d.onerror = function (a) {
                            return c(a)
                        }, d.readAsArrayBuffer(a)
                    })
                }

                function t(a, b) {
                    var c, d, e, f = a.width, h = a.height, i = b.maxWidthOrHeight, j = a;
                    return isFinite(i) && (f > i || h > i) && (c = k(f, h), j = (d = g(c, 2))[0], e = d[1], f > h ? (j.width = i, j.height = h / f * i) : (j.width = f / h * i, j.height = i), e.drawImage(a, 0, 0, j.width, j.height), q(a)), j
                }

                function u(a, b) {
                    var c = a.width, d = a.height, e = k(c, d), f = g(e, 2), h = f[0], i = f[1];
                    switch (b > 4 && 9 > b ? (h.width = d, h.height = c) : (h.width = c, h.height = d), b) {
                        case 2:
                            i.transform(-1, 0, 0, 1, c, 0);
                            break;
                        case 3:
                            i.transform(-1, 0, 0, -1, c, d);
                            break;
                        case 4:
                            i.transform(1, 0, 0, -1, 0, d);
                            break;
                        case 5:
                            i.transform(0, 1, 1, 0, 0, 0);
                            break;
                        case 6:
                            i.transform(0, 1, -1, 0, d, 0);
                            break;
                        case 7:
                            i.transform(0, -1, -1, 0, d, c);
                            break;
                        case 8:
                            i.transform(0, -1, 1, 0, 0, c)
                    }
                    return i.drawImage(a, 0, 0, c, d), q(a), h
                }

                function v(a, b) {
                    var c = arguments.length > 2 && void 0 !== arguments[2] ? arguments[2] : 0;
                    return new Promise(function (d, e) {
                        function f() {
                            var a = arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : 5;
                            if (b.signal && b.signal.aborted) throw b.signal.reason;
                            i += a, b.onProgress(Math.min(i, 100))
                        }

                        function h(a) {
                            if (b.signal && b.signal.aborted) throw b.signal.reason;
                            i = Math.min(Math.max(a, i), 100), b.onProgress(i)
                        }

                        var i, j, l, m, n, v, w, x, y, z, A, B, C, D, E, F, G, H, I, J;
                        return i = c, j = b.maxIteration || 10, l = 1024 * 1024 * b.maxSizeMB, f(), o(a, b).then(function (c) {
                            try {
                                var i;
                                return m = (i = g(c, 2))[1], f(), n = t(m, b), f(), new Promise(function (c, d) {
                                    function e() {
                                        return c(f)
                                    }

                                    var f;
                                    return (f = b.exifOrientation) ? e.call(this) : s(a).then(function (a) {
                                        try {
                                            return f = a, e.call(this)
                                        } catch (b) {
                                            return d(b)
                                        }
                                    }.bind(this), d)
                                }).then(function (c) {
                                    try {
                                        return v = c, f(), r().then(function (c) {
                                            try {
                                                return w = c ? n : u(n, v), f(), x = b.initialQuality || 1, y = b.fileType || a.type, p(w, y, a.name, a.lastModified, x).then(function (c) {
                                                    try {
                                                        var i, o = function t() {
                                                            if (j-- && (E > l || E > C)) {
                                                                var b, c, d, f;
                                                                return d = J ? .95 * I.width : I.width, f = J ? .95 * I.height : I.height, b = k(d, f), G = (c = g(b, 2))[0], (H = c[1]).drawImage(I, 0, 0, d, f), x *= "image/png" === y ? .85 : .95, p(G, y, a.name, a.lastModified, x).then(function (a) {
                                                                    try {
                                                                        return F = a, q(I), I = G, E = F.size, h(Math.min(99, Math.floor(100 * ((D - E) / (D - l))))), t
                                                                    } catch (b) {
                                                                        return e(b)
                                                                    }
                                                                }, e)
                                                            }
                                                            return [1]
                                                        }, r = function () {
                                                            return q(I), q(G), q(n), q(w), q(m), h(100), d(F)
                                                        };
                                                        return z = c, f(), A = z.size > l, B = z.size > a.size, A || B ? (C = a.size, E = D = z.size, I = w, J = !b.alwaysKeepResolution && A, (i = function (a) {
                                                            for (; a;) {
                                                                if (a.then) return void a.then(i, e);
                                                                try {
                                                                    if (a.pop) {
                                                                        if (a.length) return a.pop() ? r.call(this) : a;
                                                                        a = o
                                                                    } else a = a.call(this)
                                                                } catch (b) {
                                                                    return e(b)
                                                                }
                                                            }
                                                        }.bind(this))(o)) : (h(100), d(z))
                                                    } catch (s) {
                                                        return e(s)
                                                    }
                                                }.bind(this), e)
                                            } catch (i) {
                                                return e(i)
                                            }
                                        }.bind(this), e)
                                    } catch (i) {
                                        return e(i)
                                    }
                                }.bind(this), e)
                            } catch (o) {
                                return e(o)
                            }
                        }.bind(this), e)
                    })
                }

                function w(a, c) {
                    return new Promise(function (d, e) {
                        function g() {
                            try {
                                l.name = a.name, l.lastModified = a.lastModified
                            } catch (c) {
                            }
                            try {
                                k.preserveExif && "image/jpeg" === a.type && (!k.fileType || k.fileType && k.fileType === a.type) && (l = b(a, l))
                            } catch (e) {
                            }
                            return d(l)
                        }

                        if (k = f({}, c), m = 0, n = (j = k).onProgress, k.maxSizeMB = k.maxSizeMB || Number.POSITIVE_INFINITY, o = "boolean" != typeof k.useWebWorker || k.useWebWorker, delete k.useWebWorker, k.onProgress = function (a) {
                            m = a, "function" == typeof n && n(m)
                        }, !/^image/.test(a.type)) return e(Error("The file given is not an image"));
                        if (p = "undefined" != typeof WorkerGlobalScope && self instanceof WorkerGlobalScope, !o || "function" != typeof Worker || p) return v(a, k).then(function (a) {
                            try {
                                return l = a, g.call(this)
                            } catch (b) {
                                return e(b)
                            }
                        }.bind(this), e);
                        var h, i, j, k, l, m, n, o, p, q = function () {
                            try {
                                return g.call(this)
                            } catch (a) {
                                return e(a)
                            }
                        }.bind(this), r = function () {
                            try {
                                return v(a, k).then(function (a) {
                                    try {
                                        return l = a, q()
                                    } catch (b) {
                                        return e(b)
                                    }
                                }, e)
                            } catch (b) {
                                return e(b)
                            }
                        };
                        try {
                            return k.libURL = k.libURL || "https://cdn.jsdelivr.net/npm/browser-image-compression@2.0.2/dist/browser-image-compression.js", (h = a, i = k, new Promise(function (a, b) {
                                var c,
                                    d = "\nlet scriptImported = false\nself.addEventListener('message', async (e) => {\n  const { file, id, imageCompressionLibUrl, options } = e.data\n  options.onProgress = (progress) => self.postMessage({ progress, id })\n  try {\n    if (!scriptImported) {\n      // console.log('[worker] importScripts', imageCompressionLibUrl)\n      self.importScripts(imageCompressionLibUrl)\n      scriptImported = true\n    }\n    // console.log('[worker] self', self)\n    const compressedFile = await imageCompression(file, options)\n    self.postMessage({ file: compressedFile, id })\n  } catch (e) {\n    // console.error('[worker] error', e)\n    self.postMessage({ error: e.message + '\\n' + e.stack, id })\n  }\n})\n";
                                y || (c = [], "function" == typeof d ? c.push("(".concat(d, ")()")) : c.push(d), y = URL.createObjectURL(new Blob(c)));
                                var e = new Worker(y);
                                e.addEventListener("message", function (c) {
                                    if (i.signal && i.signal.aborted) e.terminate(); else if (void 0 === c.data.progress) {
                                        if (c.data.error) return b(Error(c.data.error)), void e.terminate();
                                        a(c.data.file), e.terminate()
                                    } else i.onProgress(c.data.progress)
                                }), e.addEventListener("error", b), i.signal && i.signal.addEventListener("abort", function () {
                                    b(i.signal.reason), e.terminate()
                                }), e.postMessage({
                                    file: h,
                                    imageCompressionLibUrl: i.libURL,
                                    options: f(f({}, i), {}, {onProgress: void 0, signal: void 0})
                                })
                            })).then(function (a) {
                                try {
                                    return l = a, q()
                                } catch (b) {
                                    return r()
                                }
                            }, r)
                        } catch (s) {
                            r()
                        }
                    })
                }

                var x, y, z, A, B, C, D, E, F = function (a) {
                    return new Promise(function (b, c) {
                        var d = new FileReader;
                        d.addEventListener("load", function (a) {
                            var d = a.target.result, e = new DataView(d), f = 0;
                            if (65496 !== e.getUint16(f)) return c("not a valid JPEG");
                            for (f += 2; ;) {
                                var g = e.getUint16(f);
                                if (65498 === g) break;
                                var h = e.getUint16(f + 2);
                                if (65505 === g && 1165519206 === e.getUint32(f + 4)) {
                                    var i = f + 10, j = void 0;
                                    switch (e.getUint16(i)) {
                                        case 18761:
                                            j = !0;
                                            break;
                                        case 19789:
                                            j = !1;
                                            break;
                                        default:
                                            return c("TIFF header contains invalid endian")
                                    }
                                    if (42 !== e.getUint16(i + 2, j)) return c("TIFF header contains invalid version");
                                    for (var k = e.getUint32(i + 4, j), l = i + k + 2 + 12 * e.getUint16(i + k, j), m = i + k + 2; l > m; m += 12) if (274 == e.getUint16(m, j)) {
                                        if (3 !== e.getUint16(m + 2, j)) return c("Orientation data type is invalid");
                                        if (1 !== e.getUint32(m + 4, j)) return c("Orientation data count is invalid");
                                        e.setUint16(m + 8, 1, j);
                                        break
                                    }
                                    return b(d.slice(f, f + 2 + h))
                                }
                                f += 2 + h
                            }
                            return b(new Blob)
                        }), d.readAsArrayBuffer(a)
                    })
                }, G = {};
                z = {
                    get exports() {
                        return G
                    }, set exports(a) {
                        G = a
                    }
                }, C = {}, z.exports = C, C.parse = function (a, b) {
                    for (var c = C.bin.readUshort, d = C.bin.readUint, e = 0, f = {}, g = new Uint8Array(a), h = g.length - 4; 101010256 != d(g, h);) h--;
                    e = h, e += 4;
                    var i = c(g, e += 4);
                    c(g, e += 2);
                    var j = d(g, e += 2), k = d(g, e += 4);
                    e += 4, e = k;
                    for (var l = 0; i > l; l++) {
                        d(g, e), e += 4, e += 4, e += 4, d(g, e += 4), j = d(g, e += 4);
                        var m = d(g, e += 4), n = c(g, e += 4), o = c(g, e + 2), p = c(g, e + 4);
                        e += 6;
                        var q = d(g, e += 8);
                        e += 4, e += n + o + p, C._readLocal(g, q, f, j, m, b)
                    }
                    return f
                }, C._readLocal = function (a, b, c, d, e, f) {
                    var g = C.bin.readUshort, h = C.bin.readUint;
                    h(a, b), g(a, b += 4), g(a, b += 2);
                    var i = g(a, b += 2);
                    h(a, b += 2), h(a, b += 4), b += 4;
                    var j = g(a, b += 8), k = g(a, b += 2);
                    b += 2;
                    var l = C.bin.readUTF8(a, b, j);
                    if (b += j, b += k, f) c[l] = {size: e, csize: d}; else {
                        var m = new Uint8Array(a.buffer, b);
                        if (0 == i) c[l] = new Uint8Array(m.buffer.slice(b, b + d)); else {
                            if (8 != i) throw"unknown compression method: " + i;
                            var n = new Uint8Array(e);
                            C.inflateRaw(m, n), c[l] = n
                        }
                    }
                }, C.inflateRaw = function (a, b) {
                    return C.F.inflate(a, b)
                }, C.inflate = function (a, b) {
                    return a[0], a[1], C.inflateRaw(new Uint8Array(a.buffer, a.byteOffset + 2, a.length - 6), b)
                }, C.deflate = function (a, b) {
                    null == b && (b = {level: 6});
                    var c = 0, d = new Uint8Array(50 + Math.floor(1.1 * a.length));
                    d[c] = 120, d[c + 1] = 156, c += 2, c = C.F.deflateRaw(a, d, c, b.level);
                    var e = C.adler(a, 0, a.length);
                    return d[c + 0] = 255 & e >>> 24, d[c + 1] = 255 & e >>> 16, d[c + 2] = 255 & e >>> 8, d[c + 3] = 255 & e >>> 0, new Uint8Array(d.buffer, 0, c + 4)
                }, C.deflateRaw = function (a, b) {
                    null == b && (b = {level: 6});
                    var c = new Uint8Array(50 + Math.floor(1.1 * a.length)), d = C.F.deflateRaw(a, c, d, b.level);
                    return new Uint8Array(c.buffer, 0, d)
                }, C.encode = function (a, b) {
                    null == b && (b = !1);
                    var c = 0, d = C.bin.writeUint, e = C.bin.writeUshort, f = {};
                    for (var g in a) {
                        var h = !C._noNeed(g) && !b, i = a[g], j = C.crc.crc(i, 0, i.length);
                        f[g] = {cpr: h, usize: i.length, crc: j, file: h ? C.deflateRaw(i) : i}
                    }
                    for (var g in f) c += f[g].file.length + 30 + 46 + 2 * C.bin.sizeUTF8(g);
                    c += 22;
                    var k = new Uint8Array(c), l = 0, m = [];
                    for (var g in f) {
                        var n = f[g];
                        m.push(l), l = C._writeHeader(k, l, g, n, 0)
                    }
                    var o = 0, p = l;
                    for (var g in f) n = f[g], m.push(l), l = C._writeHeader(k, l, g, n, 1, m[o++]);
                    var q = l - p;
                    return d(k, l, 101010256), l += 4, e(k, l += 4, o), e(k, l += 2, o), d(k, l += 2, q), d(k, l += 4, p), l += 4, l += 2, k.buffer
                }, C._noNeed = function (a) {
                    var b = a.split(".").pop().toLowerCase();
                    return -1 != "png,jpg,jpeg,zip".indexOf(b)
                }, C._writeHeader = function (a, b, c, d, e, f) {
                    var g = C.bin.writeUint, h = C.bin.writeUshort, i = d.file;
                    return g(a, b, 0 == e ? 67324752 : 33639248), b += 4, 1 == e && (b += 2), h(a, b, 20), h(a, b += 2, 0), h(a, b += 2, d.cpr ? 8 : 0), g(a, b += 2, 0), g(a, b += 4, d.crc), g(a, b += 4, i.length), g(a, b += 4, d.usize), h(a, b += 4, C.bin.sizeUTF8(c)), h(a, b += 2, 0), b += 2, 1 == e && (b += 2, b += 2, g(a, b += 6, f), b += 4), b += C.bin.writeUTF8(a, b, c), 0 == e && (a.set(i, b), b += i.length), b
                }, C.crc = {
                    table: function () {
                        for (var a = new Uint32Array(256), b = 0; 256 > b; b++) {
                            for (var c = b, d = 0; 8 > d; d++) 1 & c ? c = 3988292384 ^ c >>> 1 : c >>>= 1;
                            a[b] = c
                        }
                        return a
                    }(), update: function (a, b, c, d) {
                        for (var e = 0; d > e; e++) a = C.crc.table[255 & (a ^ b[c + e])] ^ a >>> 8;
                        return a
                    }, crc: function (a, b, c) {
                        return 4294967295 ^ C.crc.update(4294967295, a, b, c)
                    }
                }, C.adler = function (a, b, c) {
                    for (var d = 1, e = 0, f = b, g = b + c; g > f;) {
                        for (var h = Math.min(f + 5552, g); h > f;) e += d += a[f++];
                        d %= 65521, e %= 65521
                    }
                    return e << 16 | d
                }, C.bin = {
                    readUshort: function (a, b) {
                        return a[b] | a[b + 1] << 8
                    }, writeUshort: function (a, b, c) {
                        a[b] = 255 & c, a[b + 1] = 255 & c >> 8
                    }, readUint: function (a, b) {
                        return 16777216 * a[b + 3] + (a[b + 2] << 16 | a[b + 1] << 8 | a[b])
                    }, writeUint: function (a, b, c) {
                        a[b] = 255 & c, a[b + 1] = 255 & c >> 8, a[b + 2] = 255 & c >> 16, a[b + 3] = 255 & c >> 24
                    }, readASCII: function (a, b, c) {
                        for (var d = "", e = 0; c > e; e++) d += String.fromCharCode(a[b + e]);
                        return d
                    }, writeASCII: function (a, b, c) {
                        for (var d = 0; d < c.length; d++) a[b + d] = c.charCodeAt(d)
                    }, pad: function (a) {
                        return a.length < 2 ? "0" + a : a
                    }, readUTF8: function (a, b, c) {
                        for (var d, e = "", f = 0; c > f; f++) e += "%" + C.bin.pad(a[b + f].toString(16));
                        try {
                            d = decodeURIComponent(e)
                        } catch (g) {
                            return C.bin.readASCII(a, b, c)
                        }
                        return d
                    }, writeUTF8: function (a, b, c) {
                        for (var d = c.length, e = 0, f = 0; d > f; f++) {
                            var g = c.charCodeAt(f);
                            if (0 == (4294967168 & g)) a[b + e] = g, e++; else if (0 == (4294965248 & g)) a[b + e] = 192 | g >> 6, a[b + e + 1] = 128 | 63 & g >> 0, e += 2; else if (0 == (4294901760 & g)) a[b + e] = 224 | g >> 12, a[b + e + 1] = 128 | 63 & g >> 6, a[b + e + 2] = 128 | 63 & g >> 0, e += 3; else {
                                if (0 != (4292870144 & g)) throw"e";
                                a[b + e] = 240 | g >> 18, a[b + e + 1] = 128 | 63 & g >> 12, a[b + e + 2] = 128 | 63 & g >> 6, a[b + e + 3] = 128 | 63 & g >> 0, e += 4
                            }
                        }
                        return e
                    }, sizeUTF8: function (a) {
                        for (var b = a.length, c = 0, d = 0; b > d; d++) {
                            var e = a.charCodeAt(d);
                            if (0 == (4294967168 & e)) c++; else if (0 == (4294965248 & e)) c += 2; else if (0 == (4294901760 & e)) c += 3; else {
                                if (0 != (4292870144 & e)) throw"e";
                                c += 4
                            }
                        }
                        return c
                    }
                }, C.F = {}, C.F.deflateRaw = function (a, b, c, d) {
                    var e = [[0, 0, 0, 0, 0], [4, 4, 8, 4, 0], [4, 5, 16, 8, 0], [4, 6, 16, 16, 0], [4, 10, 16, 32, 0], [8, 16, 32, 32, 0], [8, 16, 128, 128, 0], [8, 32, 128, 256, 0], [32, 128, 258, 1024, 1], [32, 258, 258, 4096, 1]][d],
                        f = C.F.U, g = C.F._goodIndex;
                    C.F._hash;
                    var h = C.F._putsE, i = 0, j = c << 3, k = 0, l = a.length;
                    if (0 == d) {
                        for (; l > i;) h(b, j, i + (x = Math.min(65535, l - i)) == l ? 1 : 0), j = C.F._copyExact(a, i, x, b, j + 8), i += x;
                        return j >>> 3
                    }
                    var m = f.lits, n = f.strt, o = f.prev, p = 0, q = 0, r = 0, s = 0, t = 0, u = 0;
                    for (l > 2 && (n[u = C.F._hash(a, 0)] = 0), i = 0; l > i; i++) {
                        if (t = u, l - 2 > i + 1) {
                            u = C.F._hash(a, i + 1);
                            var v = 32767 & i + 1;
                            o[v] = n[u], n[u] = v
                        }
                        if (i >= k) {
                            (p > 14e3 || q > 26697) && l - i > 100 && (i > k && (m[p] = i - k, p += 2, k = i), j = C.F._writeBlock(i == l - 1 || k == l ? 1 : 0, m, p, s, a, r, i - r, b, j), p = q = s = 0, r = i);
                            var w = 0;
                            l - 2 > i && (w = C.F._bestMatch(a, i, o, t, Math.min(e[2], l - i), e[3]));
                            var x = w >>> 16, y = 65535 & w;
                            if (0 != w) {
                                y = 65535 & w;
                                var z = g(x = w >>> 16, f.of0);
                                f.lhst[257 + z]++;
                                var A = g(y, f.df0);
                                f.dhst[A]++, s += f.exb[z] + f.dxb[A], m[p] = x << 23 | i - k, m[p + 1] = y << 16 | z << 8 | A, p += 2, k = i + x
                            } else f.lhst[a[i]]++;
                            q++
                        }
                    }
                    for (r == i && 0 != a.length || (i > k && (m[p] = i - k, p += 2, k = i), j = C.F._writeBlock(1, m, p, s, a, r, i - r, b, j), p = 0, q = 0, p = q = s = 0, r = i); 0 != (7 & j);) j++;
                    return j >>> 3
                }, C.F._bestMatch = function (a, b, c, d, e, f) {
                    var g = 32767 & b, h = c[g], i = 32767 & g - h + 32768;
                    if (h == g || d != C.F._hash(a, b - i)) return 0;
                    for (var j = 0, k = 0, l = Math.min(32767, b); l >= i && 0 != --f && h != g;) {
                        if (0 == j || a[b + j] == a[b + j - i]) {
                            var m = C.F._howLong(a, b, i);
                            if (m > j) {
                                if (k = i, (j = m) >= e) break;
                                m > i + 2 && (m = i + 2);
                                for (var n = 0, o = 0; m - 2 > o; o++) {
                                    var p = 32767 & b - i + o + 32768, q = 32767 & p - c[p] + 32768;
                                    q > n && (n = q, h = p)
                                }
                            }
                        }
                        i += 32767 & (g = h) - (h = c[g]) + 32768
                    }
                    return j << 16 | k
                }, C.F._howLong = function (a, b, c) {
                    if (a[b] != a[b - c] || a[b + 1] != a[b + 1 - c] || a[b + 2] != a[b + 2 - c]) return 0;
                    var d = b, e = Math.min(a.length, b + 258);
                    for (b += 3; e > b && a[b] == a[b - c];) b++;
                    return b - d
                }, C.F._hash = function (a, b) {
                    return 65535 & (a[b] << 8 | a[b + 1]) + (a[b + 2] << 4)
                }, C.saved = 0, C.F._writeBlock = function (a, b, c, d, e, f, g, h, i) {
                    var j, k, l, m, n, o, p, q, r, s, t, u = C.F.U, v = C.F._putsF, w = C.F._putsE;
                    u.lhst[256]++, m = (l = C.F.getTrees())[0], n = l[1], o = l[2], p = l[3], q = l[4], r = l[5], s = l[6], t = l[7];
                    var x = 32 + (0 == (7 & i + 3) ? 0 : 8 - (7 & i + 3)) + (g << 3),
                        y = d + C.F.contSize(u.fltree, u.lhst) + C.F.contSize(u.fdtree, u.dhst),
                        z = d + C.F.contSize(u.ltree, u.lhst) + C.F.contSize(u.dtree, u.dhst);
                    z += 14 + 3 * r + C.F.contSize(u.itree, u.ihst) + (2 * u.ihst[16] + 3 * u.ihst[17] + 7 * u.ihst[18]);
                    for (var A = 0; 286 > A; A++) u.lhst[A] = 0;
                    for (A = 0; 30 > A; A++) u.dhst[A] = 0;
                    for (A = 0; 19 > A; A++) u.ihst[A] = 0;
                    var B = y > x && z > x ? 0 : z > y ? 1 : 2;
                    if (v(h, i, a), v(h, i + 1, B), i += 3, 0 == B) {
                        for (; 0 != (7 & i);) i++;
                        i = C.F._copyExact(e, f, g, h, i)
                    } else {
                        if (1 == B && (j = u.fltree, k = u.fdtree), 2 == B) {
                            C.F.makeCodes(u.ltree, m), C.F.revCodes(u.ltree, m), C.F.makeCodes(u.dtree, n), C.F.revCodes(u.dtree, n), C.F.makeCodes(u.itree, o), C.F.revCodes(u.itree, o), j = u.ltree, k = u.dtree, w(h, i, p - 257), w(h, i += 5, q - 1), w(h, i += 5, r - 4), i += 4;
                            for (var D = 0; r > D; D++) w(h, i + 3 * D, u.itree[1 + (u.ordr[D] << 1)]);
                            i += 3 * r, i = C.F._codeTiny(s, u.itree, h, i), i = C.F._codeTiny(t, u.itree, h, i)
                        }
                        for (var E = f, F = 0; c > F; F += 2) {
                            for (var G = b[F], H = G >>> 23, I = E + (8388607 & G); I > E;) i = C.F._writeLit(e[E++], j, h, i);
                            if (0 != H) {
                                var J = b[F + 1], K = J >> 16, L = 255 & J >> 8, M = 255 & J;
                                w(h, i = C.F._writeLit(257 + L, j, h, i), H - u.of0[L]), i += u.exb[L], v(h, i = C.F._writeLit(M, k, h, i), K - u.df0[M]), i += u.dxb[M], E += H
                            }
                        }
                        i = C.F._writeLit(256, j, h, i)
                    }
                    return i
                }, C.F._copyExact = function (a, b, c, d, e) {
                    var f = e >>> 3;
                    return d[f] = c, d[f + 1] = c >>> 8, d[f + 2] = 255 - d[f], d[f + 3] = 255 - d[f + 1], f += 4, d.set(new Uint8Array(a.buffer, b, c), f), e + (c + 4 << 3)
                }, C.F.getTrees = function () {
                    for (var a = C.F.U, b = C.F._hufTree(a.lhst, a.ltree, 15), c = C.F._hufTree(a.dhst, a.dtree, 15), d = [], e = C.F._lenCodes(a.ltree, d), f = [], g = C.F._lenCodes(a.dtree, f), h = 0; h < d.length; h += 2) a.ihst[d[h]]++;
                    for (h = 0; h < f.length; h += 2) a.ihst[f[h]]++;
                    for (var i = C.F._hufTree(a.ihst, a.itree, 7), j = 19; j > 4 && 0 == a.itree[1 + (a.ordr[j - 1] << 1)];) j--;
                    return [b, c, i, e, g, j, d, f]
                }, C.F.getSecond = function (a) {
                    for (var b = [], c = 0; c < a.length; c += 2) b.push(a[c + 1]);
                    return b
                }, C.F.nonZero = function (a) {
                    for (var b = "", c = 0; c < a.length; c += 2) 0 != a[c + 1] && (b += (c >> 1) + ",");
                    return b
                }, C.F.contSize = function (a, b) {
                    for (var c = 0, d = 0; d < b.length; d++) c += b[d] * a[1 + (d << 1)];
                    return c
                }, C.F._codeTiny = function (a, b, c, d) {
                    for (var e = 0; e < a.length; e += 2) {
                        var f = a[e], g = a[e + 1];
                        d = C.F._writeLit(f, b, c, d);
                        var h = 16 == f ? 2 : 17 == f ? 3 : 7;
                        f > 15 && (C.F._putsE(c, d, g, h), d += h)
                    }
                    return d
                }, C.F._lenCodes = function (a, b) {
                    for (var c = a.length; 2 != c && 0 == a[c - 1];) c -= 2;
                    for (var d = 0; c > d; d += 2) {
                        var e = a[d + 1], f = c > d + 3 ? a[d + 3] : -1, g = c > d + 5 ? a[d + 5] : -1,
                            h = 0 == d ? -1 : a[d - 1];
                        if (0 == e && f == e && g == e) {
                            for (var i = d + 5; c > i + 2 && a[i + 2] == e;) i += 2;
                            (j = Math.min(i + 1 - d >>> 1, 138)) < 11 ? b.push(17, j - 3) : b.push(18, j - 11), d += 2 * j - 2
                        } else if (e == h && f == e && g == e) {
                            for (i = d + 5; c > i + 2 && a[i + 2] == e;) i += 2;
                            var j = Math.min(i + 1 - d >>> 1, 6);
                            b.push(16, j - 3), d += 2 * j - 2
                        } else b.push(e, 0)
                    }
                    return c >>> 1
                }, C.F._hufTree = function (a, b, c) {
                    var d = [], e = a.length, f = b.length, g = 0;
                    for (g = 0; f > g; g += 2) b[g] = 0, b[g + 1] = 0;
                    for (g = 0; e > g; g++) 0 != a[g] && d.push({lit: g, f: a[g]});
                    var h = d.length, i = d.slice(0);
                    if (0 == h) return 0;
                    if (1 == h) {
                        var j = d[0].lit;
                        return i = 0 == j ? 1 : 0, b[1 + (j << 1)] = 1, b[1 + (i << 1)] = 1, 1
                    }
                    d.sort(function (a, b) {
                        return a.f - b.f
                    });
                    var k = d[0], l = d[1], m = 0, n = 1, o = 2;
                    for (d[0] = {
                        lit: -1,
                        f: k.f + l.f,
                        l: k,
                        r: l,
                        d: 0
                    }; n != h - 1;) k = m != n && (o == h || d[m].f < d[o].f) ? d[m++] : d[o++], l = m != n && (o == h || d[m].f < d[o].f) ? d[m++] : d[o++], d[n++] = {
                        lit: -1,
                        f: k.f + l.f,
                        l: k,
                        r: l
                    };
                    var p = C.F.setDepth(d[n - 1], 0);
                    for (p > c && (C.F.restrictDepth(i, c, p), p = c), g = 0; h > g; g++) b[1 + (i[g].lit << 1)] = i[g].d;
                    return p
                }, C.F.setDepth = function (a, b) {
                    return -1 != a.lit ? (a.d = b, b) : Math.max(C.F.setDepth(a.l, b + 1), C.F.setDepth(a.r, b + 1))
                }, C.F.restrictDepth = function (a, b, c) {
                    var d = 0, e = 1 << c - b, f = 0;
                    for (a.sort(function (a, b) {
                        return b.d == a.d ? a.f - b.f : b.d - a.d
                    }), d = 0; d < a.length && a[d].d > b; d++) {
                        var g = a[d].d;
                        a[d].d = b, f += e - (1 << c - g)
                    }
                    for (f >>>= c - b; f > 0;) (g = a[d].d) < b ? (a[d].d++, f -= 1 << b - g - 1) : d++;
                    for (; d >= 0; d--) a[d].d == b && 0 > f && (a[d].d--, f++);
                    0 != f && console.log("debt left")
                }, C.F._goodIndex = function (a, b) {
                    var c = 0;
                    return b[16 | c] <= a && (c |= 16), b[8 | c] <= a && (c |= 8), b[4 | c] <= a && (c |= 4), b[2 | c] <= a && (c |= 2), b[1 | c] <= a && (c |= 1), c
                }, C.F._writeLit = function (a, b, c, d) {
                    return C.F._putsF(c, d, b[a << 1]), d + b[1 + (a << 1)]
                }, C.F.inflate = function (a, b) {
                    var c = Uint8Array;
                    if (3 == a[0] && 0 == a[1]) return b || new c(0);
                    var d = C.F, e = d._bitsF, f = d._bitsE, g = d._decodeTiny, h = d.makeCodes, i = d.codes2map,
                        j = d._get17, k = d.U, l = null == b;
                    l && (b = new c(a.length >>> 2 << 3));
                    for (var m, n, o = 0, p = 0, q = 0, r = 0, s = 0, t = 0, u = 0, v = 0, w = 0; 0 == o;) if (o = e(a, w, 1), p = e(a, w + 1, 2), w += 3, 0 != p) {
                        if (l && (b = C.F._check(b, v + 131072)), 1 == p && (m = k.flmap, n = k.fdmap, t = 511, u = 31), 2 == p) {
                            q = f(a, w, 5) + 257, r = f(a, w + 5, 5) + 1, s = f(a, w + 10, 4) + 4, w += 14;
                            for (var x = 0; 38 > x; x += 2) k.itree[x] = 0, k.itree[x + 1] = 0;
                            var y = 1;
                            for (x = 0; s > x; x++) {
                                var z = f(a, w + 3 * x, 3);
                                k.itree[1 + (k.ordr[x] << 1)] = z, z > y && (y = z)
                            }
                            w += 3 * s, h(k.itree, y), i(k.itree, y, k.imap), m = k.lmap, n = k.dmap, w = g(k.imap, (1 << y) - 1, q + r, a, w, k.ttree);
                            var A = d._copyOut(k.ttree, 0, q, k.ltree);
                            t = (1 << A) - 1;
                            var B = d._copyOut(k.ttree, q, r, k.dtree);
                            u = (1 << B) - 1, h(k.ltree, A), i(k.ltree, A, m), h(k.dtree, B), i(k.dtree, B, n)
                        }
                        for (; ;) {
                            var D = m[j(a, w) & t];
                            w += 15 & D;
                            var E = D >>> 4;
                            if (0 == E >>> 8) b[v++] = E; else {
                                if (256 == E) break;
                                var F = v + E - 254;
                                if (E > 264) {
                                    var G = k.ldef[E - 257];
                                    F = v + (G >>> 3) + f(a, w, 7 & G), w += 7 & G
                                }
                                var H = n[j(a, w) & u];
                                w += 15 & H;
                                var I = H >>> 4, J = k.ddef[I], K = (J >>> 4) + e(a, w, 15 & J);
                                for (w += 15 & J, l && (b = C.F._check(b, v + 131072)); F > v;) b[v] = b[v++ - K], b[v] = b[v++ - K], b[v] = b[v++ - K], b[v] = b[v++ - K];
                                v = F
                            }
                        }
                    } else {
                        0 != (7 & w) && (w += 8 - (7 & w));
                        var L = 4 + (w >>> 3), M = a[L - 4] | a[L - 3] << 8;
                        l && (b = C.F._check(b, v + M)), b.set(new c(a.buffer, a.byteOffset + L, M), v), w = L + M << 3, v += M
                    }
                    return b.length == v ? b : b.slice(0, v)
                }, C.F._check = function (a, b) {
                    var c = a.length;
                    if (c >= b) return a;
                    var d = new Uint8Array(Math.max(c << 1, b));
                    return d.set(a, 0), d
                }, C.F._decodeTiny = function (a, b, c, d, e, f) {
                    for (var g = C.F._bitsE, h = C.F._get17, i = 0; c > i;) {
                        var j = a[h(d, e) & b];
                        e += 15 & j;
                        var k = j >>> 4;
                        if (15 >= k) f[i] = k, i++; else {
                            var l = 0, m = 0;
                            16 == k ? (m = 3 + g(d, e, 2), e += 2, l = f[i - 1]) : 17 == k ? (m = 3 + g(d, e, 3), e += 3) : 18 == k && (m = 11 + g(d, e, 7), e += 7);
                            for (var n = i + m; n > i;) f[i] = l, i++
                        }
                    }
                    return e
                }, C.F._copyOut = function (a, b, c, d) {
                    for (var e = 0, f = 0, g = d.length >>> 1; c > f;) {
                        var h = a[f + b];
                        d[f << 1] = 0, d[1 + (f << 1)] = h, h > e && (e = h), f++
                    }
                    for (; g > f;) d[f << 1] = 0, d[1 + (f << 1)] = 0, f++;
                    return e
                }, C.F.makeCodes = function (a, b) {
                    for (var c, d, e, f, g = C.F.U, h = a.length, i = g.bl_count, j = 0; b >= j; j++) i[j] = 0;
                    for (j = 1; h > j; j += 2) i[a[j]]++;
                    var k = g.next_code;
                    for (c = 0, i[0] = 0, d = 1; b >= d; d++) c = c + i[d - 1] << 1, k[d] = c;
                    for (e = 0; h > e; e += 2) 0 != (f = a[e + 1]) && (a[e] = k[f], k[f]++)
                }, C.F.codes2map = function (a, b, c) {
                    for (var d = a.length, e = C.F.U.rev15, f = 0; d > f; f += 2) if (0 != a[f + 1]) for (var g = f >> 1, h = a[f + 1], i = g << 4 | h, j = b - h, k = a[f] << j, l = k + (1 << j); k != l;) c[e[k] >>> 15 - b] = i, k++
                }, C.F.revCodes = function (a, b) {
                    for (var c = C.F.U.rev15, d = 15 - b, e = 0; e < a.length; e += 2) {
                        var f = a[e] << b - a[e + 1];
                        a[e] = c[f] >>> d
                    }
                }, C.F._putsE = function (a, b, c) {
                    c <<= 7 & b;
                    var d = b >>> 3;
                    a[d] |= c, a[d + 1] |= c >>> 8
                }, C.F._putsF = function (a, b, c) {
                    c <<= 7 & b;
                    var d = b >>> 3;
                    a[d] |= c, a[d + 1] |= c >>> 8, a[d + 2] |= c >>> 16
                }, C.F._bitsE = function (a, b, c) {
                    return (a[b >>> 3] | a[1 + (b >>> 3)] << 8) >>> (7 & b) & (1 << c) - 1
                }, C.F._bitsF = function (a, b, c) {
                    return (a[b >>> 3] | a[1 + (b >>> 3)] << 8 | a[2 + (b >>> 3)] << 16) >>> (7 & b) & (1 << c) - 1
                }, C.F._get17 = function (a, b) {
                    return (a[b >>> 3] | a[1 + (b >>> 3)] << 8 | a[2 + (b >>> 3)] << 16) >>> (7 & b)
                }, C.F._get25 = function (a, b) {
                    return (a[b >>> 3] | a[1 + (b >>> 3)] << 8 | a[2 + (b >>> 3)] << 16 | a[3 + (b >>> 3)] << 24) >>> (7 & b)
                }, C.F.U = (A = Uint16Array, B = Uint32Array, {
                    next_code: new A(16),
                    bl_count: new A(16),
                    ordr: [16, 17, 18, 0, 8, 7, 9, 6, 10, 5, 11, 4, 12, 3, 13, 2, 14, 1, 15],
                    of0: [3, 4, 5, 6, 7, 8, 9, 10, 11, 13, 15, 17, 19, 23, 27, 31, 35, 43, 51, 59, 67, 83, 99, 115, 131, 163, 195, 227, 258, 999, 999, 999],
                    exb: [0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 4, 4, 4, 5, 5, 5, 5, 0, 0, 0, 0],
                    ldef: new A(32),
                    df0: [1, 2, 3, 4, 5, 7, 9, 13, 17, 25, 33, 49, 65, 97, 129, 193, 257, 385, 513, 769, 1025, 1537, 2049, 3073, 4097, 6145, 8193, 12289, 16385, 24577, 65535, 65535],
                    dxb: [0, 0, 0, 0, 1, 1, 2, 2, 3, 3, 4, 4, 5, 5, 6, 6, 7, 7, 8, 8, 9, 9, 10, 10, 11, 11, 12, 12, 13, 13, 0, 0],
                    ddef: new B(32),
                    flmap: new A(512),
                    fltree: [],
                    fdmap: new A(32),
                    fdtree: [],
                    lmap: new A(32768),
                    ltree: [],
                    ttree: [],
                    dmap: new A(32768),
                    dtree: [],
                    imap: new A(512),
                    itree: [],
                    rev15: new A(32768),
                    lhst: new B(286),
                    dhst: new B(30),
                    ihst: new B(19),
                    lits: new B(15e3),
                    strt: new A(65536),
                    prev: new A(32768)
                }), function () {
                    function a(a, b, c) {
                        for (; 0 != b--;) a.push(0, c)
                    }

                    for (var b = C.F.U, c = 0; 32768 > c; c++) {
                        var d = c;
                        d = (4278255360 & (d = (4042322160 & (d = (3435973836 & (d = (2863311530 & d) >>> 1 | (1431655765 & d) << 1)) >>> 2 | (858993459 & d) << 2)) >>> 4 | (252645135 & d) << 4)) >>> 8 | (16711935 & d) << 8, b.rev15[c] = (d >>> 16 | d << 16) >>> 17
                    }
                    for (c = 0; 32 > c; c++) b.ldef[c] = b.of0[c] << 3 | b.exb[c], b.ddef[c] = b.df0[c] << 4 | b.dxb[c];
                    a(b.fltree, 144, 8), a(b.fltree, 112, 9), a(b.fltree, 24, 7), a(b.fltree, 8, 8), C.F.makeCodes(b.fltree, 9), C.F.codes2map(b.fltree, 9, b.flmap), C.F.revCodes(b.fltree, 9), a(b.fdtree, 32, 5), C.F.makeCodes(b.fdtree, 5), C.F.codes2map(b.fdtree, 5, b.fdmap), C.F.revCodes(b.fdtree, 5), a(b.itree, 19, 0), a(b.ltree, 286, 0), a(b.dtree, 30, 0), a(b.ttree, 320, 0)
                }();
                var H = (D = {__proto__: null, "default": G}, (E = [G]).forEach(function (a) {
                    a && "string" != typeof a && !Array.isArray(a) && Object.keys(a).forEach(function (b) {
                        if ("default" !== b && !(b in D)) {
                            var c = Object.getOwnPropertyDescriptor(a, b);
                            Object.defineProperty(D, b, c.get ? c : {
                                enumerable: !0, get: function () {
                                    return a[b]
                                }
                            })
                        }
                    })
                }), Object.freeze(D)), I = function () {
                    function a(a, b, c, e) {
                        var f = b * c, g = Math.ceil(b * d(e) / 8), h = new Uint8Array(4 * f),
                            i = new Uint32Array(h.buffer), j = e.ctype, k = e.depth, m = l.readUshort;
                        if (6 == j) {
                            var n = f << 2;
                            if (8 == k) for (var o = 0; n > o; o += 4) h[o] = a[o], h[o + 1] = a[o + 1], h[o + 2] = a[o + 2], h[o + 3] = a[o + 3];
                            if (16 == k) for (o = 0; n > o; o++) h[o] = a[o << 1]
                        } else if (2 == j) {
                            var p = e.tabs.tRNS;
                            if (null == p) {
                                if (8 == k) for (o = 0; f > o; o++) {
                                    var q = 3 * o;
                                    i[o] = -16777216 | a[q + 2] << 16 | a[q + 1] << 8 | a[q]
                                }
                                if (16 == k) for (o = 0; f > o; o++) q = 6 * o, i[o] = -16777216 | a[q + 4] << 16 | a[q + 2] << 8 | a[q]
                            } else {
                                var r = p[0], s = p[1], t = p[2];
                                if (8 == k) for (o = 0; f > o; o++) {
                                    var u = o << 2;
                                    q = 3 * o, i[o] = -16777216 | a[q + 2] << 16 | a[q + 1] << 8 | a[q], a[q] == r && a[q + 1] == s && a[q + 2] == t && (h[u + 3] = 0)
                                }
                                if (16 == k) for (o = 0; f > o; o++) u = o << 2, q = 6 * o, i[o] = -16777216 | a[q + 4] << 16 | a[q + 2] << 8 | a[q], m(a, q) == r && m(a, q + 2) == s && m(a, q + 4) == t && (h[u + 3] = 0)
                            }
                        } else if (3 == j) {
                            var v, w = e.tabs.PLTE, x = e.tabs.tRNS, y = x ? x.length : 0;
                            if (1 == k) for (var z = 0; c > z; z++) {
                                var A = z * g, B = z * b;
                                for (o = 0; b > o; o++) {
                                    u = B + o << 2;
                                    var C = 3 * (v = 1 & a[A + (o >> 3)] >> 7 - ((7 & o) << 0));
                                    h[u] = w[C], h[u + 1] = w[C + 1], h[u + 2] = w[C + 2], h[u + 3] = y > v ? x[v] : 255
                                }
                            }
                            if (2 == k) for (z = 0; c > z; z++) for (A = z * g, B = z * b, o = 0; b > o; o++) u = B + o << 2, C = 3 * (v = 3 & a[A + (o >> 2)] >> 6 - ((3 & o) << 1)), h[u] = w[C], h[u + 1] = w[C + 1], h[u + 2] = w[C + 2], h[u + 3] = y > v ? x[v] : 255;
                            if (4 == k) for (z = 0; c > z; z++) for (A = z * g, B = z * b, o = 0; b > o; o++) u = B + o << 2, C = 3 * (v = 15 & a[A + (o >> 1)] >> 4 - ((1 & o) << 2)), h[u] = w[C], h[u + 1] = w[C + 1], h[u + 2] = w[C + 2], h[u + 3] = y > v ? x[v] : 255;
                            if (8 == k) for (o = 0; f > o; o++) u = o << 2, C = 3 * (v = a[o]), h[u] = w[C], h[u + 1] = w[C + 1], h[u + 2] = w[C + 2], h[u + 3] = y > v ? x[v] : 255
                        } else if (4 == j) {
                            if (8 == k) for (o = 0; f > o; o++) {
                                u = o << 2;
                                var D, E = a[D = o << 1];
                                h[u] = E, h[u + 1] = E, h[u + 2] = E, h[u + 3] = a[D + 1]
                            }
                            if (16 == k) for (o = 0; f > o; o++) u = o << 2, E = a[D = o << 2], h[u] = E, h[u + 1] = E, h[u + 2] = E, h[u + 3] = a[D + 2]
                        } else if (0 == j) for (r = e.tabs.tRNS ? e.tabs.tRNS : -1, z = 0; c > z; z++) {
                            var F = z * g, G = z * b;
                            if (1 == k) for (var H = 0; b > H; H++) {
                                var I = (E = 255 * (1 & a[F + (H >>> 3)] >>> 7 - (7 & H))) == 255 * r ? 0 : 255;
                                i[G + H] = I << 24 | E << 16 | E << 8 | E
                            } else if (2 == k) for (H = 0; b > H; H++) I = (E = 85 * (3 & a[F + (H >>> 2)] >>> 6 - ((3 & H) << 1))) == 85 * r ? 0 : 255, i[G + H] = I << 24 | E << 16 | E << 8 | E; else if (4 == k) for (H = 0; b > H; H++) I = (E = 17 * (15 & a[F + (H >>> 1)] >>> 4 - ((1 & H) << 2))) == 17 * r ? 0 : 255, i[G + H] = I << 24 | E << 16 | E << 8 | E; else if (8 == k) for (H = 0; b > H; H++) I = (E = a[F + H]) == r ? 0 : 255, i[G + H] = I << 24 | E << 16 | E << 8 | E; else if (16 == k) for (H = 0; b > H; H++) E = a[F + (H << 1)], I = m(a, F + (H << 1)) == r ? 0 : 255, i[G + H] = I << 24 | E << 16 | E << 8 | E
                        }
                        return h
                    }

                    function b(a, b, f, g) {
                        var h = d(a), i = new Uint8Array((Math.ceil(f * h / 8) + 1 + a.interlace) * g);
                        return b = a.tabs.CgBI ? m(b, i) : c(b, i), 0 == a.interlace ? b = e(b, a, 0, f, g) : 1 == a.interlace && (b = function (a, b) {
                            for (var c = b.width, f = b.height, g = d(b), h = g >> 3, i = Math.ceil(c * g / 8), j = new Uint8Array(f * i), k = 0, l = [0, 0, 4, 0, 2, 0, 1], m = [0, 4, 0, 2, 0, 1, 0], n = [8, 8, 8, 4, 4, 2, 2], o = [8, 8, 4, 4, 2, 2, 1], p = 0; 7 > p;) {
                                for (var q = n[p], r = o[p], s = 0, t = 0, u = l[p]; f > u;) u += q, t++;
                                for (var v = m[p]; c > v;) v += r, s++;
                                var w = Math.ceil(s * g / 8);
                                e(a, b, k, s, t);
                                for (var x = 0, y = l[p]; f > y;) {
                                    for (var z, A = m[p], B = k + x * w << 3; c > A;) {
                                        if (1 == g && (z = 1 & (z = a[B >> 3]) >> 7 - (7 & B), j[y * i + (A >> 3)] |= z << 7 - ((7 & A) << 0)), 2 == g && (z = 3 & (z = a[B >> 3]) >> 6 - (7 & B), j[y * i + (A >> 2)] |= z << 6 - ((3 & A) << 1)), 4 == g && (z = 15 & (z = a[B >> 3]) >> 4 - (7 & B), j[y * i + (A >> 1)] |= z << 4 - ((1 & A) << 2)), g >= 8) for (var C = y * i + A * h, D = 0; h > D; D++) j[C + D] = a[(B >> 3) + D];
                                        B += g, A += r
                                    }
                                    x++, y += q
                                }
                                0 != s * t && (k += t * (1 + w)), p += 1
                            }
                            return j
                        }(b, a)), b
                    }

                    function c(a, b) {
                        return m(new Uint8Array(a.buffer, 2, a.length - 6), b)
                    }

                    function d(a) {
                        return [1, null, 3, 1, 2, null, 4][a.ctype] * a.depth
                    }

                    function e(a, b, c, e, g) {
                        var h, i, j = d(b), k = Math.ceil(e * j / 8);
                        j = Math.ceil(j / 8);
                        var l = a[c], m = 0;
                        if (l > 1 && (a[c] = [0, 0, 1][l - 2]), 3 == l) for (m = j; k > m; m++) a[m + 1] = 255 & a[m + 1] + (a[m + 1 - j] >>> 1);
                        for (var n = 0; g > n; n++) if (l = a[(i = (h = c + n * k) + n + 1) - 1], m = 0, 0 == l) for (; k > m; m++) a[h + m] = a[i + m]; else if (1 == l) {
                            for (; j > m; m++) a[h + m] = a[i + m];
                            for (; k > m; m++) a[h + m] = a[i + m] + a[h + m - j]
                        } else if (2 == l) for (; k > m; m++) a[h + m] = a[i + m] + a[h + m - k]; else if (3 == l) {
                            for (; j > m; m++) a[h + m] = a[i + m] + (a[h + m - k] >>> 1);
                            for (; k > m; m++) a[h + m] = a[i + m] + (a[h + m - k] + a[h + m - j] >>> 1)
                        } else {
                            for (; j > m; m++) a[h + m] = a[i + m] + f(0, a[h + m - k], 0);
                            for (; k > m; m++) a[h + m] = a[i + m] + f(a[h + m - j], a[h + m - k], a[h + m - j - k])
                        }
                        return a
                    }

                    function f(a, b, c) {
                        var d = a + b - c, e = d - a, f = d - b, g = d - c;
                        return f * f >= e * e && g * g >= e * e ? a : g * g >= f * f ? b : c
                    }

                    function g(a, b, c) {
                        c.width = l.readUint(a, b), b += 4, c.height = l.readUint(a, b), b += 4, c.depth = a[b], b++, c.ctype = a[b], b++, c.compress = a[b], b++, c.filter = a[b], b++, c.interlace = a[b], b++
                    }

                    function h(a, b, c, d, e, f, g, h, i) {
                        for (var j = Math.min(b, e), k = Math.min(c, f), l = 0, m = 0, n = 0; k > n; n++) for (var o = 0; j > o; o++) if (g >= 0 && h >= 0 ? (l = n * b + o << 2, m = (h + n) * e + g + o << 2) : (l = (-h + n) * b - g + o << 2, m = n * e + o << 2), 0 == i) d[m] = a[l], d[m + 1] = a[l + 1], d[m + 2] = a[l + 2], d[m + 3] = a[l + 3]; else if (1 == i) {
                            var p = a[l + 3] * (1 / 255), q = a[l] * p, r = a[l + 1] * p, s = a[l + 2] * p,
                                t = d[m + 3] * (1 / 255), u = d[m] * t, v = d[m + 1] * t, w = d[m + 2] * t, x = 1 - p,
                                y = p + t * x, z = 0 == y ? 0 : 1 / y;
                            d[m + 3] = 255 * y, d[m + 0] = (q + u * x) * z, d[m + 1] = (r + v * x) * z, d[m + 2] = (s + w * x) * z
                        } else if (2 == i) p = a[l + 3], q = a[l], r = a[l + 1], s = a[l + 2], t = d[m + 3], u = d[m], v = d[m + 1], w = d[m + 2], p == t && q == u && r == v && s == w ? (d[m] = 0, d[m + 1] = 0, d[m + 2] = 0, d[m + 3] = 0) : (d[m] = q, d[m + 1] = r, d[m + 2] = s, d[m + 3] = p); else if (3 == i) {
                            if (p = a[l + 3], q = a[l], r = a[l + 1], s = a[l + 2], t = d[m + 3], u = d[m], v = d[m + 1], w = d[m + 2], p == t && q == u && r == v && s == w) continue;
                            if (220 > p && t > 20) return !1
                        }
                        return !0
                    }

                    var i, j, k, l = {
                        nextZero: function (a, b) {
                            for (; 0 != a[b];) b++;
                            return b
                        }, readUshort: function (a, b) {
                            return a[b] << 8 | a[b + 1]
                        }, writeUshort: function (a, b, c) {
                            a[b] = 255 & c >> 8, a[b + 1] = 255 & c
                        }, readUint: function (a, b) {
                            return 16777216 * a[b] + (a[b + 1] << 16 | a[b + 2] << 8 | a[b + 3])
                        }, writeUint: function (a, b, c) {
                            a[b] = 255 & c >> 24, a[b + 1] = 255 & c >> 16, a[b + 2] = 255 & c >> 8, a[b + 3] = 255 & c
                        }, readASCII: function (a, b, c) {
                            for (var d = "", e = 0; c > e; e++) d += String.fromCharCode(a[b + e]);
                            return d
                        }, writeASCII: function (a, b, c) {
                            for (var d = 0; d < c.length; d++) a[b + d] = c.charCodeAt(d)
                        }, readBytes: function (a, b, c) {
                            for (var d = [], e = 0; c > e; e++) d.push(a[b + e]);
                            return d
                        }, pad: function (a) {
                            return a.length < 2 ? "0".concat(a) : a
                        }, readUTF8: function (a, b, c) {
                            for (var d, e = "", f = 0; c > f; f++) e += "%".concat(l.pad(a[b + f].toString(16)));
                            try {
                                d = decodeURIComponent(e)
                            } catch (g) {
                                return l.readASCII(a, b, c)
                            }
                            return d
                        }
                    }, m = ((i = {H: {}}).H.N = function (a, b) {
                        var c, d, e = Uint8Array, f = 0, g = 0, h = 0, j = 0, k = 0, l = 0, m = 0, n = 0, o = 0;
                        if (3 == a[0] && 0 == a[1]) return b || new e(0);
                        var p = i.H, q = p.b, r = p.e, s = p.R, t = p.n, u = p.A, v = p.Z, w = p.m, x = null == b;
                        for (x && (b = new e(a.length >>> 2 << 5)); 0 == f;) if (f = q(a, o, 1), g = q(a, o + 1, 2), o += 3, 0 != g) {
                            if (x && (b = i.H.W(b, n + 131072)), 1 == g && (c = w.J, d = w.h, l = 511, m = 31), 2 == g) {
                                h = r(a, o, 5) + 257, j = r(a, o + 5, 5) + 1, k = r(a, o + 10, 4) + 4, o += 14;
                                for (var y = 1, z = 0; 38 > z; z += 2) w.Q[z] = 0, w.Q[z + 1] = 0;
                                for (z = 0; k > z; z++) {
                                    var A = r(a, o + 3 * z, 3);
                                    w.Q[1 + (w.X[z] << 1)] = A, A > y && (y = A)
                                }
                                o += 3 * k, t(w.Q, y), u(w.Q, y, w.u), c = w.w, d = w.d, o = s(w.u, (1 << y) - 1, h + j, a, o, w.v);
                                var B = p.V(w.v, 0, h, w.C);
                                l = (1 << B) - 1;
                                var C = p.V(w.v, h, j, w.D);
                                m = (1 << C) - 1, t(w.C, B), u(w.C, B, c), t(w.D, C), u(w.D, C, d)
                            }
                            for (; ;) {
                                var D = c[v(a, o) & l];
                                o += 15 & D;
                                var E = D >>> 4;
                                if (0 == E >>> 8) b[n++] = E; else {
                                    if (256 == E) break;
                                    var F = n + E - 254;
                                    if (E > 264) {
                                        var G = w.q[E - 257];
                                        F = n + (G >>> 3) + r(a, o, 7 & G), o += 7 & G
                                    }
                                    var H = d[v(a, o) & m];
                                    o += 15 & H;
                                    var I = H >>> 4, J = w.c[I], K = (J >>> 4) + q(a, o, 15 & J);
                                    for (o += 15 & J; F > n;) b[n] = b[n++ - K], b[n] = b[n++ - K], b[n] = b[n++ - K], b[n] = b[n++ - K];
                                    n = F
                                }
                            }
                        } else {
                            0 != (7 & o) && (o += 8 - (7 & o));
                            var L = 4 + (o >>> 3), M = a[L - 4] | a[L - 3] << 8;
                            x && (b = i.H.W(b, n + M)), b.set(new e(a.buffer, a.byteOffset + L, M), n), o = L + M << 3, n += M
                        }
                        return b.length == n ? b : b.slice(0, n)
                    }, i.H.W = function (a, b) {
                        var c = a.length;
                        if (c >= b) return a;
                        var d = new Uint8Array(c << 1);
                        return d.set(a, 0), d
                    }, i.H.R = function (a, b, c, d, e, f) {
                        for (var g = i.H.e, h = i.H.Z, j = 0; c > j;) {
                            var k = a[h(d, e) & b];
                            e += 15 & k;
                            var l = k >>> 4;
                            if (15 >= l) f[j] = l, j++; else {
                                var m = 0, n = 0;
                                16 == l ? (n = 3 + g(d, e, 2), e += 2, m = f[j - 1]) : 17 == l ? (n = 3 + g(d, e, 3), e += 3) : 18 == l && (n = 11 + g(d, e, 7), e += 7);
                                for (var o = j + n; o > j;) f[j] = m, j++
                            }
                        }
                        return e
                    }, i.H.V = function (a, b, c, d) {
                        for (var e = 0, f = 0, g = d.length >>> 1; c > f;) {
                            var h = a[f + b];
                            d[f << 1] = 0, d[1 + (f << 1)] = h, h > e && (e = h), f++
                        }
                        for (; g > f;) d[f << 1] = 0, d[1 + (f << 1)] = 0, f++;
                        return e
                    }, i.H.n = function (a, b) {
                        for (var c, d, e, f, g = i.H.m, h = a.length, j = g.j, k = 0; b >= k; k++) j[k] = 0;
                        for (k = 1; h > k; k += 2) j[a[k]]++;
                        var l = g.K;
                        for (c = 0, j[0] = 0, d = 1; b >= d; d++) c = c + j[d - 1] << 1, l[d] = c;
                        for (e = 0; h > e; e += 2) 0 != (f = a[e + 1]) && (a[e] = l[f], l[f]++)
                    }, i.H.A = function (a, b, c) {
                        for (var d = a.length, e = i.H.m.r, f = 0; d > f; f += 2) if (0 != a[f + 1]) for (var g = f >> 1, h = a[f + 1], j = g << 4 | h, k = b - h, l = a[f] << k, m = l + (1 << k); l != m;) c[e[l] >>> 15 - b] = j, l++
                    }, i.H.l = function (a, b) {
                        for (var c = i.H.m.r, d = 15 - b, e = 0; e < a.length; e += 2) {
                            var f = a[e] << b - a[e + 1];
                            a[e] = c[f] >>> d
                        }
                    }, i.H.M = function (a, b, c) {
                        c <<= 7 & b;
                        var d = b >>> 3;
                        a[d] |= c, a[d + 1] |= c >>> 8
                    }, i.H.I = function (a, b, c) {
                        c <<= 7 & b;
                        var d = b >>> 3;
                        a[d] |= c, a[d + 1] |= c >>> 8, a[d + 2] |= c >>> 16
                    }, i.H.e = function (a, b, c) {
                        return (a[b >>> 3] | a[1 + (b >>> 3)] << 8) >>> (7 & b) & (1 << c) - 1
                    }, i.H.b = function (a, b, c) {
                        return (a[b >>> 3] | a[1 + (b >>> 3)] << 8 | a[2 + (b >>> 3)] << 16) >>> (7 & b) & (1 << c) - 1
                    }, i.H.Z = function (a, b) {
                        return (a[b >>> 3] | a[1 + (b >>> 3)] << 8 | a[2 + (b >>> 3)] << 16) >>> (7 & b)
                    }, i.H.i = function (a, b) {
                        return (a[b >>> 3] | a[1 + (b >>> 3)] << 8 | a[2 + (b >>> 3)] << 16 | a[3 + (b >>> 3)] << 24) >>> (7 & b)
                    }, i.H.m = (j = Uint16Array, k = Uint32Array, {
                        K: new j(16),
                        j: new j(16),
                        X: [16, 17, 18, 0, 8, 7, 9, 6, 10, 5, 11, 4, 12, 3, 13, 2, 14, 1, 15],
                        S: [3, 4, 5, 6, 7, 8, 9, 10, 11, 13, 15, 17, 19, 23, 27, 31, 35, 43, 51, 59, 67, 83, 99, 115, 131, 163, 195, 227, 258, 999, 999, 999],
                        T: [0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 4, 4, 4, 5, 5, 5, 5, 0, 0, 0, 0],
                        q: new j(32),
                        p: [1, 2, 3, 4, 5, 7, 9, 13, 17, 25, 33, 49, 65, 97, 129, 193, 257, 385, 513, 769, 1025, 1537, 2049, 3073, 4097, 6145, 8193, 12289, 16385, 24577, 65535, 65535],
                        z: [0, 0, 0, 0, 1, 1, 2, 2, 3, 3, 4, 4, 5, 5, 6, 6, 7, 7, 8, 8, 9, 9, 10, 10, 11, 11, 12, 12, 13, 13, 0, 0],
                        c: new k(32),
                        J: new j(512),
                        _: [],
                        h: new j(32),
                        $: [],
                        w: new j(32768),
                        C: [],
                        v: [],
                        d: new j(32768),
                        D: [],
                        u: new j(512),
                        Q: [],
                        r: new j(32768),
                        s: new k(286),
                        Y: new k(30),
                        a: new k(19),
                        t: new k(15e3),
                        k: new j(65536),
                        g: new j(32768)
                    }), function () {
                        function a(a, b, c) {
                            for (; 0 != b--;) a.push(0, c)
                        }

                        for (var b = i.H.m, c = 0; 32768 > c; c++) {
                            var d = c;
                            d = (4278255360 & (d = (4042322160 & (d = (3435973836 & (d = (2863311530 & d) >>> 1 | (1431655765 & d) << 1)) >>> 2 | (858993459 & d) << 2)) >>> 4 | (252645135 & d) << 4)) >>> 8 | (16711935 & d) << 8, b.r[c] = (d >>> 16 | d << 16) >>> 17
                        }
                        for (c = 0; 32 > c; c++) b.q[c] = b.S[c] << 3 | b.T[c], b.c[c] = b.p[c] << 4 | b.z[c];
                        a(b._, 144, 8), a(b._, 112, 9), a(b._, 24, 7), a(b._, 8, 8), i.H.n(b._, 9), i.H.A(b._, 9, b.J), i.H.l(b._, 9), a(b.$, 32, 5), i.H.n(b.$, 5), i.H.A(b.$, 5, b.h), i.H.l(b.$, 5), a(b.Q, 19, 0), a(b.C, 286, 0), a(b.D, 30, 0), a(b.v, 320, 0)
                    }(), i.H.N);
                    return {
                        decode: function (a) {
                            for (var d, e, f = new Uint8Array(a), h = 8, i = l, j = i.readUshort, k = i.readUint, n = {
                                tabs: {},
                                frames: []
                            }, o = new Uint8Array(f.length), p = 0, q = 0, r = [137, 80, 78, 71, 13, 10, 26, 10], s = 0; 8 > s; s++) if (f[s] != r[s]) throw"The input is not a PNG file!";
                            for (; h < f.length;) {
                                var t = i.readUint(f, h);
                                h += 4;
                                var u = i.readASCII(f, h, 4);
                                if (h += 4, "IHDR" == u) g(f, h, n); else if ("iCCP" == u) {
                                    for (var v = h; 0 != f[v];) v++;
                                    i.readASCII(f, h, v - h), f[v + 1];
                                    var w = f.slice(v + 2, h + t), x = null;
                                    try {
                                        x = c(w)
                                    } catch (y) {
                                        x = m(w)
                                    }
                                    n.tabs[u] = x
                                } else if ("CgBI" == u) n.tabs[u] = f.slice(h, h + 4); else if ("IDAT" == u) {
                                    for (s = 0; t > s; s++) o[p + s] = f[h + s];
                                    p += t
                                } else if ("acTL" == u) n.tabs[u] = {
                                    num_frames: k(f, h),
                                    num_plays: k(f, h + 4)
                                }, e = new Uint8Array(f.length); else if ("fcTL" == u) {
                                    0 != q && ((d = n.frames[n.frames.length - 1]).data = b(n, e.slice(0, q), d.rect.width, d.rect.height), q = 0);
                                    var z = {x: k(f, h + 12), y: k(f, h + 16), width: k(f, h + 4), height: k(f, h + 8)},
                                        A = j(f, h + 22);
                                    A = j(f, h + 20) / (0 == A ? 100 : A);
                                    var B = {rect: z, delay: Math.round(1e3 * A), dispose: f[h + 24], blend: f[h + 25]};
                                    n.frames.push(B)
                                } else if ("fdAT" == u) {
                                    for (s = 0; t - 4 > s; s++) e[q + s] = f[h + s + 4];
                                    q += t - 4
                                } else if ("pHYs" == u) n.tabs[u] = [i.readUint(f, h), i.readUint(f, h + 4), f[h + 8]]; else if ("cHRM" == u) for (s = 0, n.tabs[u] = []; 8 > s; s++) n.tabs[u].push(i.readUint(f, h + 4 * s)); else if ("tEXt" == u || "zTXt" == u) {
                                    null == n.tabs[u] && (n.tabs[u] = {});
                                    var C = i.nextZero(f, h), D = i.readASCII(f, h, C - h), E = h + t - C - 1;
                                    if ("tEXt" == u) G = i.readASCII(f, C + 1, E); else {
                                        var F = c(f.slice(C + 2, C + 2 + E));
                                        G = i.readUTF8(F, 0, F.length)
                                    }
                                    n.tabs[u][D] = G
                                } else if ("iTXt" == u) {
                                    null == n.tabs[u] && (n.tabs[u] = {}), C = 0, v = h, C = i.nextZero(f, v), D = i.readASCII(f, v, C - v);
                                    var G, H = f[v = C + 1];
                                    f[v + 1], v += 2, C = i.nextZero(f, v), i.readASCII(f, v, C - v), v = C + 1, C = i.nextZero(f, v), i.readUTF8(f, v, C - v), E = t - ((v = C + 1) - h), 0 == H ? G = i.readUTF8(f, v, E) : (F = c(f.slice(v, v + E)), G = i.readUTF8(F, 0, F.length)), n.tabs[u][D] = G
                                } else if ("PLTE" == u) n.tabs[u] = i.readBytes(f, h, t); else if ("hIST" == u) {
                                    var I = n.tabs.PLTE.length / 3;
                                    for (s = 0, n.tabs[u] = []; I > s; s++) n.tabs[u].push(j(f, h + 2 * s))
                                } else if ("tRNS" == u) 3 == n.ctype ? n.tabs[u] = i.readBytes(f, h, t) : 0 == n.ctype ? n.tabs[u] = j(f, h) : 2 == n.ctype && (n.tabs[u] = [j(f, h), j(f, h + 2), j(f, h + 4)]); else if ("gAMA" == u) n.tabs[u] = i.readUint(f, h) / 1e5; else if ("sRGB" == u) n.tabs[u] = f[h]; else if ("bKGD" == u) 0 == n.ctype || 4 == n.ctype ? n.tabs[u] = [j(f, h)] : 2 == n.ctype || 6 == n.ctype ? n.tabs[u] = [j(f, h), j(f, h + 2), j(f, h + 4)] : 3 == n.ctype && (n.tabs[u] = f[h]); else if ("IEND" == u) break;
                                h += t, i.readUint(f, h), h += 4
                            }
                            return 0 != q && ((d = n.frames[n.frames.length - 1]).data = b(n, e.slice(0, q), d.rect.width, d.rect.height)), n.data = b(n, o, n.width, n.height), delete n.compress, delete n.interlace, delete n.filter, n
                        }, toRGBA8: function (b) {
                            var c = b.width, d = b.height;
                            if (null == b.tabs.acTL) return [a(b.data, c, d, b).buffer];
                            var e = [];
                            null == b.frames[0].data && (b.frames[0].data = b.data);
                            for (var f = 4 * c * d, g = new Uint8Array(f), i = new Uint8Array(f), j = new Uint8Array(f), k = 0; k < b.frames.length; k++) {
                                var l = b.frames[k], m = l.rect.x, n = l.rect.y, o = l.rect.width, p = l.rect.height,
                                    q = a(l.data, o, p, b);
                                if (0 != k) for (var r = 0; f > r; r++) j[r] = g[r];
                                if (0 == l.blend ? h(q, o, p, g, c, d, m, n, 0) : 1 == l.blend && h(q, o, p, g, c, d, m, n, 1), e.push(g.buffer.slice(0)), 0 == l.dispose) ; else if (1 == l.dispose) h(i, o, p, g, c, d, m, n, 0); else if (2 == l.dispose) for (r = 0; f > r; r++) g[r] = j[r]
                            }
                            return e
                        }, _paeth: f, _copyTile: h, _bin: l
                    }
                }();
                !function () {
                    function a(a, b, c, d) {
                        b[c] += a[0] * d >> 4, b[c + 1] += a[1] * d >> 4, b[c + 2] += a[2] * d >> 4, b[c + 3] += a[3] * d >> 4
                    }

                    function b(a) {
                        return Math.max(0, Math.min(255, a))
                    }

                    function c(a, b) {
                        var c = a[0] - b[0], d = a[1] - b[1], e = a[2] - b[2], f = a[3] - b[3];
                        return c * c + d * d + e * e + f * f
                    }

                    function d(d, e, f, g, h, i, j) {
                        null == j && (j = 1);
                        for (var k = g.length, l = [], m = 0; k > m; m++) {
                            var n = g[m];
                            l.push([255 & n >>> 0, 255 & n >>> 8, 255 & n >>> 16, 255 & n >>> 24])
                        }
                        for (m = 0; k > m; m++) for (var o = 4294967295, p = 0, q = 0; k > q; q++) {
                            var r = c(l[m], l[q]);
                            q != m && o > r && (o = r, p = q)
                        }
                        var s = new Uint32Array(h.buffer), t = new Int16Array(4 * e * f),
                            u = [0, 8, 2, 10, 12, 4, 14, 6, 3, 11, 1, 9, 15, 7, 13, 5];
                        for (m = 0; m < u.length; m++) u[m] = 255 * ((u[m] + .5) / 16 - .5);
                        for (var v = 0; f > v; v++) for (var w = 0; e > w; w++) {
                            m = 4 * (v * e + w), 2 != j ? x = [b(d[m] + t[m]), b(d[m + 1] + t[m + 1]), b(d[m + 2] + t[m + 2]), b(d[m + 3] + t[m + 3])] : (r = u[4 * (3 & v) + (3 & w)], x = [b(d[m] + r), b(d[m + 1] + r), b(d[m + 2] + r), b(d[m + 3] + r)]), p = 0;
                            var x, y = 16777215;
                            for (q = 0; k > q; q++) {
                                var z = c(x, l[q]);
                                y > z && (y = z, p = q)
                            }
                            var A = l[p], B = [x[0] - A[0], x[1] - A[1], x[2] - A[2], x[3] - A[3]];
                            1 == j && (w != e - 1 && a(B, t, m + 4, 7), v != f - 1 && (0 != w && a(B, t, m + 4 * e - 4, 3), a(B, t, m + 4 * e, 5), w != e - 1 && a(B, t, m + 4 * e + 4, 1))), i[m >> 2] = p, s[m >> 2] = g[p]
                        }
                    }

                    function e(a, b, c, d, e) {
                        null == e && (e = {});
                        var f, g = w.crc, h = u.writeUint, i = u.writeUshort, j = u.writeASCII, k = 8,
                            l = a.frames.length > 1, m = !1, n = 33 + (l ? 20 : 0);
                        if (null != e.sRGB && (n += 13), null != e.pHYs && (n += 21), null != e.iCCP && (n += 21 + (f = pako.deflate(e.iCCP)).length + 4), 3 == a.ctype) {
                            for (var o = a.plte.length, p = 0; o > p; p++) 255 != a.plte[p] >>> 24 && (m = !0);
                            n += 8 + 3 * o + 4 + (m ? 8 + 1 * o + 4 : 0)
                        }
                        for (var q = 0; q < a.frames.length; q++) l && (n += 38), n += (C = a.frames[q]).cimg.length + 12, 0 != q && (n += 4);
                        n += 12;
                        var r = new Uint8Array(n), s = [137, 80, 78, 71, 13, 10, 26, 10];
                        for (p = 0; 8 > p; p++) r[p] = s[p];
                        if (h(r, k, 13), j(r, k += 4, "IHDR"), h(r, k += 4, b), h(r, k += 4, c), r[k += 4] = a.depth, r[++k] = a.ctype, r[++k] = 0, r[++k] = 0, r[++k] = 0, h(r, ++k, g(r, k - 17, 17)), k += 4, null != e.sRGB && (h(r, k, 1), j(r, k += 4, "sRGB"), r[k += 4] = e.sRGB, h(r, ++k, g(r, k - 5, 5)), k += 4), null != e.iCCP) {
                            var t = 13 + f.length;
                            h(r, k, t), j(r, k += 4, "iCCP"), j(r, k += 4, "ICC profile"), k += 11, k += 2, r.set(f, k), h(r, k += f.length, g(r, k - (t + 4), t + 4)), k += 4
                        }
                        if (null != e.pHYs && (h(r, k, 9), j(r, k += 4, "pHYs"), h(r, k += 4, e.pHYs[0]), h(r, k += 4, e.pHYs[1]), r[k += 4] = e.pHYs[2], h(r, ++k, g(r, k - 13, 13)), k += 4), l && (h(r, k, 8), j(r, k += 4, "acTL"), h(r, k += 4, a.frames.length), h(r, k += 4, null != e.loop ? e.loop : 0), h(r, k += 4, g(r, k - 12, 12)), k += 4), 3 == a.ctype) {
                            for (h(r, k, 3 * (o = a.plte.length)), j(r, k += 4, "PLTE"), k += 4, p = 0; o > p; p++) {
                                var v = 3 * p, x = a.plte[p], y = 255 & x, z = 255 & x >>> 8, A = 255 & x >>> 16;
                                r[k + v + 0] = y, r[k + v + 1] = z, r[k + v + 2] = A
                            }
                            if (h(r, k += 3 * o, g(r, k - 3 * o - 4, 3 * o + 4)), k += 4, m) {
                                for (h(r, k, o), j(r, k += 4, "tRNS"), k += 4, p = 0; o > p; p++) r[k + p] = 255 & a.plte[p] >>> 24;
                                h(r, k += o, g(r, k - o - 4, o + 4)), k += 4
                            }
                        }
                        var B = 0;
                        for (q = 0; q < a.frames.length; q++) {
                            var C = a.frames[q];
                            l && (h(r, k, 26), j(r, k += 4, "fcTL"), h(r, k += 4, B++), h(r, k += 4, C.rect.width), h(r, k += 4, C.rect.height), h(r, k += 4, C.rect.x), h(r, k += 4, C.rect.y), i(r, k += 4, d[q]), i(r, k += 2, 1e3), r[k += 2] = C.dispose, r[++k] = C.blend, h(r, ++k, g(r, k - 30, 30)), k += 4);
                            var D = C.cimg;
                            h(r, k, (o = D.length) + (0 == q ? 0 : 4));
                            var E = k += 4;
                            j(r, k, 0 == q ? "IDAT" : "fdAT"), k += 4, 0 != q && (h(r, k, B++), k += 4), r.set(D, k), h(r, k += o, g(r, E, k - E)), k += 4
                        }
                        return h(r, k, 0), j(r, k += 4, "IEND"), h(r, k += 4, g(r, k - 4, 4)), k += 4, r.buffer
                    }

                    function f(a, b, c) {
                        for (var d = 0; d < a.frames.length; d++) {
                            var e = a.frames[d];
                            e.rect.width;
                            var f = e.rect.height, g = new Uint8Array(f * e.bpl + f);
                            e.cimg = j(e.img, f, e.bpp, e.bpl, g, b, c)
                        }
                    }

                    function g(a, b, c, e, f) {
                        for (var g = f[0], j = f[1], k = f[2], m = f[3], n = f[4], o = f[5], p = 6, q = 8, r = 255, s = 0; s < a.length; s++) for (var u = new Uint8Array(a[s]), v = u.length, w = 0; v > w; w += 4) r &= u[w + 3];
                        var x = 255 != r, y = function (a, b, c, d, e, f) {
                            for (var g, j = [], k = 0; k < a.length; k++) {
                                var l, m = new Uint8Array(a[k]), n = new Uint32Array(m.buffer), o = 0, p = 0, q = b,
                                    r = c, s = d ? 1 : 0;
                                if (0 != k) {
                                    for (var u = f || d || 1 == k || 0 != j[k - 2].dispose ? 1 : 2, v = 0, w = 1e9, x = 0; u > x; x++) {
                                        for (var y = new Uint8Array(a[k - 1 - x]), z = new Uint32Array(a[k - 1 - x]), A = b, B = c, C = -1, D = -1, E = 0; c > E; E++) for (var F = 0; b > F; F++) n[N = E * b + F] != z[N] && (A > F && (A = F), F > C && (C = F), B > E && (B = E), E > D && (D = E));
                                        -1 == C && (A = B = C = D = 0), e && (1 == (1 & A) && A--, 1 == (1 & B) && B--);
                                        var G = (C - A + 1) * (D - B + 1);
                                        w > G && (w = G, v = x, o = A, p = B, q = C - A + 1, r = D - B + 1)
                                    }
                                    y = new Uint8Array(a[k - 1 - v]), 1 == v && (j[k - 1].dispose = 2), l = new Uint8Array(4 * q * r), t(y, b, c, l, q, r, -o, -p, 0), 1 == (s = t(m, b, c, l, q, r, -o, -p, 3) ? 1 : 0) ? i(m, b, c, l, {
                                        x: o,
                                        y: p,
                                        width: q,
                                        height: r
                                    }) : t(m, b, c, l, q, r, -o, -p, 0)
                                } else l = m.slice(0);
                                j.push({rect: {x: o, y: p, width: q, height: r}, img: l, blend: s, dispose: 0})
                            }
                            if (d) for (k = 0; k < j.length; k++) if (1 != (g = j[k]).blend) {
                                var H = g.rect, I = j[k - 1].rect, J = Math.min(H.x, I.x), K = Math.min(H.y, I.y), L = {
                                    x: J,
                                    y: K,
                                    width: Math.max(H.x + H.width, I.x + I.width) - J,
                                    height: Math.max(H.y + H.height, I.y + I.height) - K
                                };
                                j[k - 1].dispose = 1, 0 != k - 1 && h(a, b, c, j, k - 1, L, e), h(a, b, c, j, k, L, e)
                            }
                            var M = 0;
                            if (1 != a.length) for (var N = 0; N < j.length; N++) M += (g = j[N]).rect.width * g.rect.height;
                            return j
                        }(a, b, c, g, j, k), z = {}, A = [], B = [];
                        if (0 != e) {
                            var C = [];
                            for (w = 0; w < y.length; w++) C.push(y[w].img.buffer);
                            var D = function (a) {
                                for (var b = 0, c = 0; c < a.length; c++) b += a[c].byteLength;
                                var d = new Uint8Array(b), e = 0;
                                for (c = 0; c < a.length; c++) {
                                    for (var f = new Uint8Array(a[c]), g = f.length, h = 0; g > h; h += 4) {
                                        var i = f[h], j = f[h + 1], k = f[h + 2], l = f[h + 3];
                                        0 == l && (i = j = k = 0), d[e + h] = i, d[e + h + 1] = j, d[e + h + 2] = k, d[e + h + 3] = l
                                    }
                                    e += g
                                }
                                return d.buffer
                            }(C), E = l(D, e);
                            for (w = 0; w < E.plte.length; w++) A.push(E.plte[w].est.rgba);
                            var F = 0;
                            for (w = 0; w < y.length; w++) {
                                var G = (J = y[w]).img.length, H = new Uint8Array(E.inds.buffer, F >> 2, G >> 2);
                                B.push(H);
                                var I = new Uint8Array(E.abuf, F, G);
                                o && d(J.img, J.rect.width, J.rect.height, A, I, H), J.img.set(I), F += G
                            }
                        } else for (s = 0; s < y.length; s++) {
                            var J = y[s], K = new Uint32Array(J.img.buffer), L = J.rect.width;
                            for (v = K.length, H = new Uint8Array(v), B.push(H), w = 0; v > w; w++) {
                                var M = K[w];
                                if (0 != w && M == K[w - 1]) H[w] = H[w - 1]; else if (w > L && M == K[w - L]) H[w] = H[w - L]; else {
                                    var N = z[M];
                                    if (null == N && (z[M] = N = A.length, A.push(M), A.length >= 300)) break;
                                    H[w] = N
                                }
                            }
                        }
                        var O = A.length;
                        for (256 >= O && 0 == n && (q = Math.max(q = 2 >= O ? 1 : 4 >= O ? 2 : 16 >= O ? 4 : 8, m)), s = 0; s < y.length; s++) {
                            (J = y[s]).rect.x, J.rect.y, L = J.rect.width;
                            var P = J.rect.height, Q = J.img;
                            new Uint32Array(Q.buffer);
                            var R = 4 * L, S = 4;
                            if (256 >= O && 0 == n) {
                                R = Math.ceil(q * L / 8);
                                for (var T = new Uint8Array(R * P), U = B[s], V = 0; P > V; V++) {
                                    w = V * R;
                                    var W = V * L;
                                    if (8 == q) for (var X = 0; L > X; X++) T[w + X] = U[W + X]; else if (4 == q) for (X = 0; L > X; X++) T[w + (X >> 1)] |= U[W + X] << 4 - 4 * (1 & X); else if (2 == q) for (X = 0; L > X; X++) T[w + (X >> 2)] |= U[W + X] << 6 - 2 * (3 & X); else if (1 == q) for (X = 0; L > X; X++) T[w + (X >> 3)] |= U[W + X] << 7 - 1 * (7 & X)
                                }
                                Q = T, p = 3, S = 1
                            } else if (0 == x && 1 == y.length) {
                                T = new Uint8Array(3 * L * P);
                                var Y = L * P;
                                for (w = 0; Y > w; w++) {
                                    var Z = 3 * w, $ = 4 * w;
                                    T[Z] = Q[$], T[Z + 1] = Q[$ + 1], T[Z + 2] = Q[$ + 2]
                                }
                                Q = T, p = 2, S = 3, R = 3 * L
                            }
                            J.img = Q, J.bpl = R, J.bpp = S
                        }
                        return {ctype: p, depth: q, plte: A, frames: y}
                    }

                    function h(a, b, c, d, e, f, g) {
                        for (var h = Uint8Array, j = Uint32Array, k = new h(a[e - 1]), l = new j(a[e - 1]), m = e + 1 < a.length ? new h(a[e + 1]) : null, n = new h(a[e]), o = new j(n.buffer), p = b, q = c, r = -1, s = -1, u = 0; u < f.height; u++) for (var v = 0; v < f.width; v++) {
                            var w = f.x + v, x = f.y + u, y = x * b + w, z = o[y];
                            0 == z || 0 == d[e - 1].dispose && l[y] == z && (null == m || 0 != m[4 * y + 3]) || (p > w && (p = w), w > r && (r = w), q > x && (q = x), x > s && (s = x))
                        }
                        -1 == r && (p = q = r = s = 0), g && (1 == (1 & p) && p--, 1 == (1 & q) && q--), f = {
                            x: p,
                            y: q,
                            width: r - p + 1,
                            height: s - q + 1
                        };
                        var A = d[e];
                        A.rect = f, A.blend = 1, A.img = new Uint8Array(4 * f.width * f.height), 0 == d[e - 1].dispose ? (t(k, b, c, A.img, f.width, f.height, -f.x, -f.y, 0), i(n, b, c, A.img, f)) : t(n, b, c, A.img, f.width, f.height, -f.x, -f.y, 0)
                    }

                    function i(a, b, c, d, e) {
                        t(a, b, c, d, e.width, e.height, -e.x, -e.y, 2)
                    }

                    function j(a, b, c, d, e, f, g) {
                        var h, i = [], j = [0, 1, 2, 3, 4];
                        -1 != f ? j = [f] : (b * d > 5e5 || 1 == c) && (j = [0]), g && (h = {level: 0});
                        for (var l = H, m = 0; m < j.length; m++) {
                            for (var n = 0; b > n; n++) k(e, a, n, d, c, j[m]);
                            i.push(l.deflate(e, h))
                        }
                        var o, p = 1e9;
                        for (m = 0; m < i.length; m++) i[m].length < p && (o = m, p = i[m].length);
                        return i[o]
                    }

                    function k(a, b, c, d, e, f) {
                        var g = c * d, h = g + c;
                        if (a[h] = f, h++, 0 == f) if (500 > d) for (var i = 0; d > i; i++) a[h + i] = b[g + i]; else a.set(new Uint8Array(b.buffer, g, d), h); else if (1 == f) {
                            for (i = 0; e > i; i++) a[h + i] = b[g + i];
                            for (i = e; d > i; i++) a[h + i] = 255 & b[g + i] - b[g + i - e] + 256
                        } else if (0 == c) {
                            for (i = 0; e > i; i++) a[h + i] = b[g + i];
                            if (2 == f) for (i = e; d > i; i++) a[h + i] = b[g + i];
                            if (3 == f) for (i = e; d > i; i++) a[h + i] = 255 & b[g + i] - (b[g + i - e] >> 1) + 256;
                            if (4 == f) for (i = e; d > i; i++) a[h + i] = 255 & b[g + i] - v(b[g + i - e], 0, 0) + 256
                        } else {
                            if (2 == f) for (i = 0; d > i; i++) a[h + i] = 255 & b[g + i] + 256 - b[g + i - d];
                            if (3 == f) {
                                for (i = 0; e > i; i++) a[h + i] = 255 & b[g + i] + 256 - (b[g + i - d] >> 1);
                                for (i = e; d > i; i++) a[h + i] = 255 & b[g + i] + 256 - (b[g + i - d] + b[g + i - e] >> 1)
                            }
                            if (4 == f) {
                                for (i = 0; e > i; i++) a[h + i] = 255 & b[g + i] + 256 - v(0, b[g + i - d], 0);
                                for (i = e; d > i; i++) a[h + i] = 255 & b[g + i] + 256 - v(b[g + i - e], b[g + i - d], b[g + i - e - d])
                            }
                        }
                    }

                    function l(a, b) {
                        var c, d = new Uint8Array(a), e = d.slice(0), f = new Uint32Array(e.buffer), g = m(e, b),
                            h = g[0], i = g[1], j = d.length, k = new Uint8Array(j >> 2);
                        if (d.length < 2e7) for (var l = 0; j > l; l += 4) c = n(h, p = d[l] * (1 / 255), q = d[l + 1] * (1 / 255), r = d[l + 2] * (1 / 255), s = d[l + 3] * (1 / 255)), k[l >> 2] = c.ind, f[l >> 2] = c.est.rgba; else for (l = 0; j > l; l += 4) {
                            var p = d[l] * (1 / 255), q = d[l + 1] * (1 / 255), r = d[l + 2] * (1 / 255),
                                s = d[l + 3] * (1 / 255);
                            for (c = h; c.left;) c = 0 >= o(c.est, p, q, r, s) ? c.left : c.right;
                            k[l >> 2] = c.ind, f[l >> 2] = c.est.rgba
                        }
                        return {abuf: e.buffer, inds: k, plte: i}
                    }

                    function m(a, b, c) {
                        null == c && (c = 1e-4);
                        var d = new Uint32Array(a.buffer),
                            e = {i0: 0, i1: a.length, bst: null, est: null, tdst: 0, left: null, right: null};
                        e.bst = r(a, e.i0, e.i1), e.est = s(e.bst);
                        for (var f = [e]; f.length < b;) {
                            for (var g = 0, h = 0, i = 0; i < f.length; i++) f[i].est.L > g && (g = f[i].est.L, h = i);
                            if (c > g) break;
                            var j = f[h], k = p(a, d, j.i0, j.i1, j.est.e, j.est.eMq255);
                            if (j.i0 >= k || j.i1 <= k) j.est.L = 0; else {
                                var l = {i0: j.i0, i1: k, bst: null, est: null, tdst: 0, left: null, right: null};
                                l.bst = r(a, l.i0, l.i1), l.est = s(l.bst);
                                var m = {i0: k, i1: j.i1, bst: null, est: null, tdst: 0, left: null, right: null};
                                for (i = 0, m.bst = {
                                    R: [],
                                    m: [],
                                    N: j.bst.N - l.bst.N
                                }; 16 > i; i++) m.bst.R[i] = j.bst.R[i] - l.bst.R[i];
                                for (i = 0; 4 > i; i++) m.bst.m[i] = j.bst.m[i] - l.bst.m[i];
                                m.est = s(m.bst), j.left = l, j.right = m, f[h] = l, f.push(m)
                            }
                        }
                        for (f.sort(function (a, b) {
                            return b.bst.N - a.bst.N
                        }), i = 0; i < f.length; i++) f[i].ind = i;
                        return [e, f]
                    }

                    function n(a, b, c, d, e) {
                        if (null == a.left) {
                            var f, g, h, i, j, k, l, m, p;
                            return a.tdst = (f = a.est.q, g = b, h = c, i = d, j = e, k = g - f[0], l = h - f[1], m = i - f[2], k * k + l * l + m * m + (p = j - f[3]) * p), a
                        }
                        var q = o(a.est, b, c, d, e), r = a.left, s = a.right;
                        q > 0 && (r = a.right, s = a.left);
                        var t = n(r, b, c, d, e);
                        if (t.tdst <= q * q) return t;
                        var u = n(s, b, c, d, e);
                        return u.tdst < t.tdst ? u : t
                    }

                    function o(a, b, c, d, e) {
                        var f = a.e;
                        return f[0] * b + f[1] * c + f[2] * d + f[3] * e - a.eMq
                    }

                    function p(a, b, c, d, e, f) {
                        for (d -= 4; d > c;) {
                            for (; q(a, c, e) <= f;) c += 4;
                            for (; q(a, d, e) > f;) d -= 4;
                            if (c >= d) break;
                            var g = b[c >> 2];
                            b[c >> 2] = b[d >> 2], b[d >> 2] = g, c += 4, d -= 4
                        }
                        for (; q(a, c, e) > f;) c -= 4;
                        return c + 4
                    }

                    function q(a, b, c) {
                        return a[b] * c[0] + a[b + 1] * c[1] + a[b + 2] * c[2] + a[b + 3] * c[3]
                    }

                    function r(a, b, c) {
                        for (var d = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0], e = [0, 0, 0, 0], f = b; c > f; f += 4) {
                            var g = a[f] * (1 / 255), h = a[f + 1] * (1 / 255), i = a[f + 2] * (1 / 255),
                                j = a[f + 3] * (1 / 255);
                            e[0] += g, e[1] += h, e[2] += i, e[3] += j, d[0] += g * g, d[1] += g * h, d[2] += g * i, d[3] += g * j, d[5] += h * h, d[6] += h * i, d[7] += h * j, d[10] += i * i, d[11] += i * j, d[15] += j * j
                        }
                        return d[4] = d[1], d[8] = d[2], d[9] = d[6], d[12] = d[3], d[13] = d[7], d[14] = d[11], {
                            R: d,
                            m: e,
                            N: c - b >> 2
                        }
                    }

                    function s(a) {
                        var b = a.R, c = a.m, d = a.N, e = c[0], f = c[1], g = c[2], h = c[3], i = 0 == d ? 0 : 1 / d,
                            j = [b[0] - e * e * i, b[1] - e * f * i, b[2] - e * g * i, b[3] - e * h * i, b[4] - f * e * i, b[5] - f * f * i, b[6] - f * g * i, b[7] - f * h * i, b[8] - g * e * i, b[9] - g * f * i, b[10] - g * g * i, b[11] - g * h * i, b[12] - h * e * i, b[13] - h * f * i, b[14] - h * g * i, b[15] - h * h * i],
                            k = j, l = x, m = [Math.random(), Math.random(), Math.random(), Math.random()], n = 0,
                            o = 0;
                        if (0 != d) for (var p = 0; 16 > p && (m = l.multVec(k, m), o = Math.sqrt(l.dot(m, m)), m = l.sml(1 / o, m), !(0 != p && 1e-9 > Math.abs(o - n))); p++) n = o;
                        var q = [e * i, f * i, g * i, h * i];
                        return {
                            Cov: j,
                            q: q,
                            e: m,
                            L: n,
                            eMq255: l.dot(l.sml(255, q), m),
                            eMq: l.dot(m, q),
                            rgba: (Math.round(255 * q[3]) << 24 | Math.round(255 * q[2]) << 16 | Math.round(255 * q[1]) << 8 | Math.round(255 * q[0]) << 0) >>> 0
                        }
                    }

                    var t = I._copyTile, u = I._bin, v = I._paeth, w = {
                        table: function () {
                            for (var a = new Uint32Array(256), b = 0; 256 > b; b++) {
                                for (var c = b, d = 0; 8 > d; d++) 1 & c ? c = 3988292384 ^ c >>> 1 : c >>>= 1;
                                a[b] = c
                            }
                            return a
                        }(), update: function (a, b, c, d) {
                            for (var e = 0; d > e; e++) a = w.table[255 & (a ^ b[c + e])] ^ a >>> 8;
                            return a
                        }, crc: function (a, b, c) {
                            return 4294967295 ^ w.update(4294967295, a, b, c)
                        }
                    }, x = {
                        multVec: function (a, b) {
                            return [a[0] * b[0] + a[1] * b[1] + a[2] * b[2] + a[3] * b[3], a[4] * b[0] + a[5] * b[1] + a[6] * b[2] + a[7] * b[3], a[8] * b[0] + a[9] * b[1] + a[10] * b[2] + a[11] * b[3], a[12] * b[0] + a[13] * b[1] + a[14] * b[2] + a[15] * b[3]]
                        }, dot: function (a, b) {
                            return a[0] * b[0] + a[1] * b[1] + a[2] * b[2] + a[3] * b[3]
                        }, sml: function (a, b) {
                            return [a * b[0], a * b[1], a * b[2], a * b[3]]
                        }
                    };
                    I.encode = function (a, b, c, d, h, i, j) {
                        null == d && (d = 0), null == j && (j = !1);
                        var k = g(a, b, c, d, [!1, !1, !1, 0, j, !1]);
                        return f(k, -1), e(k, b, c, h, i)
                    }, I.encodeLL = function (a, b, c, d, g, h, i, j) {
                        for (var k = {
                            ctype: 0 + (1 == d ? 0 : 2) + (0 == g ? 0 : 4),
                            depth: h,
                            frames: []
                        }, l = (d + g) * h, m = l * b, n = 0; n < a.length; n++) k.frames.push({
                            rect: {
                                x: 0,
                                y: 0,
                                width: b,
                                height: c
                            },
                            img: new Uint8Array(a[n]),
                            blend: 0,
                            dispose: 1,
                            bpp: Math.ceil(l / 8),
                            bpl: Math.ceil(m / 8)
                        });
                        return f(k, 0, !0), e(k, b, c, i, j)
                    }, I.encode.compress = g, I.encode.dither = d, I.quantize = l, I.quantize.getKDtree = m, I.quantize.getNearest = n
                }();
                var J = {
                        toArrayBuffer: function (a, b) {
                            function c(a) {
                                r.setUint16(u, a, !0), u += 2
                            }

                            function d(a) {
                                r.setUint32(u, a, !0), u += 4
                            }

                            var e, f, g, h, i = a.width, j = a.height, k = i << 2,
                                l = a.getContext("2d").getImageData(0, 0, i, j), m = new Uint32Array(l.data.buffer),
                                n = (32 * i + 31) / 32 << 2, o = n * j, p = 122 + o, q = new ArrayBuffer(p),
                                r = new DataView(q), s = 1048576, t = 0, u = 0, v = 0;
                            c(19778), d(p), u += 4, d(122), d(108), d(i), d(-j >>> 0), c(1), c(32), d(3), d(o), d(2835), d(2835), u += 8, d(16711680), d(65280), d(255), d(4278190080), d(1466527264), function w() {
                                for (; j > t && s > 0;) {
                                    for (h = 122 + t * n, e = 0; k > e;) s--, g = (f = m[v++]) >>> 24, r.setUint32(h + e, f << 8 | g), e += 4;
                                    t++
                                }
                                v < m.length ? (s = 1048576, setTimeout(w, J._dly)) : b(q)
                            }()
                        }, toBlob: function (a, b) {
                            this.toArrayBuffer(a, function (a) {
                                b(new Blob([a], {type: "image/bmp"}))
                            })
                        }, _dly: 9
                    }, K = {
                        CHROME: "CHROME",
                        FIREFOX: "FIREFOX",
                        DESKTOP_SAFARI: "DESKTOP_SAFARI",
                        IE: "IE",
                        IOS: "IOS",
                        ETC: "ETC"
                    },
                    L = (m(x = {}, K.CHROME, 16384), m(x, K.FIREFOX, 11180), m(x, K.DESKTOP_SAFARI, 16384), m(x, K.IE, 8192), m(x, K.IOS, 4096), m(x, K.ETC, 8192), x),
                    M = "undefined" != typeof a,
                    N = "undefined" != typeof WorkerGlobalScope && self instanceof WorkerGlobalScope,
                    O = M && a.cordova && a.cordova.require && a.cordova.require("cordova/modulemapper"),
                    P = ((M || N) && (O && O.getOriginalSymbol(a, "File") || "undefined" != typeof File && File), (M || N) && (O && O.getOriginalSymbol(a, "FileReader") || "undefined" != typeof FileReader && FileReader));
                return w.getDataUrlFromFile = e, w.getFilefromDataUrl = c, w.loadImage = h, w.drawImageInCanvas = l, w.drawFileInCanvas = o, w.canvasToFile = p, w.getExifOrientation = s, w.handleMaxWidthOrHeight = t, w.followExifOrientation = u, w.cleanupCanvasMemory = q, w.isAutoOrientationInBrowser = r, w.approximateBelowMaximumCanvasSizeOfBrowser = j, w.copyExifWithoutOrientation = b, w.getBrowserName = i, w.version = "2.0.2", w
            })()
        }();
        var f, g = b.$;
        return f = function (a) {
            var b = 0, c = [], d = function () {
                for (var d; c.length && a > b;) d = c.shift(), b += d[0], d[1]()
            };
            return function (a, e, f) {
                c.push([e, f]), a.once("destroy", function () {
                    b -= e, setTimeout(d, 1)
                }), setTimeout(d, 1)
            }
        }(5242880), g.extend(c.options, {
            thumb: {
                width: 110,
                height: 110,
                quality: 70,
                allowMagnify: !0,
                crop: !0,
                preserveHeaders: !1,
                type: "image/jpeg"
            }, compress: {enable: !1, maxWidthOrHeight: 2e3, maxSize: 10485760}
        }), c.register({
            name: "image", makeThumb: function (a, b, c, e) {
                var h, i;
                return a = this.request("get-file", a), a.type.match(/^image/) ? (h = g.extend({}, this.options.thumb), g.isPlainObject(c) && (h = g.extend(h, c), c = null), c = c || h.width, e = e || h.height, i = new d(h), i.once("load", function () {
                    a._info = a._info || i.info(), a._meta = a._meta || i.meta(), 1 >= c && c > 0 && (c = a._info.width * c), 1 >= e && e > 0 && (e = a._info.height * e), i.resize(c, e)
                }), i.once("complete", function () {
                    b(!1, i.getAsDataUrl(h.type)), i.destroy()
                }), i.once("error", function (a) {
                    b(a || !0), i.destroy()
                }), f(i, a.source.size, function () {
                    a._info && i.info(a._info), a._meta && i.meta(a._meta), i.loadFromBlob(a.source)
                }), void 0) : (b(!0), void 0)
            }, beforeSendFile: function (a) {
                var c, d = this.options.compress;
                console.log('webuploader.compress',JSON.stringify(d,null,2));
                return a = this.request("get-file", a), d && d.enable && ~"image/jpeg,image/jpg,image/png".indexOf(a.type) && !a._compressed ? (d = g.extend({}, d), c = b.Deferred(), e(a.source.source, {
                    maxSizeMB: d.maxSize / 1024 / 1024,
                    maxWidthOrHeight: d.maxWidthOrHeight
                }).then(function (b) {
                    d.debug && console.log("webuploader.compress", (100 * (b.size / a.size)).toFixed(2) + "%");
                    var e = a.size;
                    a.source.source = b, a.source.size = b.size, a.size = b.size, a.trigger("resize", b.size, e), a._compressed = !0, c.resolve()
                }).catch(function (a) {
                    console.error("webuploader.compress.error", a), c.resolve()
                }), c.promise()) : void 0
            }
        })
    }), b("file", ["base", "mediator"], function (a, b) {
        function c() {
            return f + g++
        }

        function d(a) {
            this.name = a.name || "Untitled", this.size = a.size || 0, this.type = a.type || "application/octet-stream", this.lastModifiedDate = a.lastModifiedDate || 1 * new Date, this.id = c(), this.ext = h.exec(this.name) ? RegExp.$1 : "", this.statusText = "", i[this.id] = d.Status.INITED, this.source = a, this.loaded = 0, this.on("error", function (a) {
                this.setStatus(d.Status.ERROR, a)
            })
        }

        var e = a.$, f = "WU_FILE_", g = 0, h = /\.([^.]+)$/, i = {};
        return e.extend(d.prototype, {
            setStatus: function (a, b) {
                var c = i[this.id];
                "undefined" != typeof b && (this.statusText = b), a !== c && (i[this.id] = a, this.trigger("statuschange", a, c))
            }, getStatus: function () {
                return i[this.id]
            }, getSource: function () {
                return this.source
            }, destroy: function () {
                this.off(), delete i[this.id]
            }
        }), b.installTo(d.prototype), d.Status = {
            INITED: "inited",
            QUEUED: "queued",
            PROGRESS: "progress",
            ERROR: "error",
            COMPLETE: "complete",
            CANCELLED: "cancelled",
            INTERRUPT: "interrupt",
            INVALID: "invalid"
        }, d
    }), b("queue", ["base", "mediator", "file"], function (a, b, c) {
        function d() {
            this.stats = {
                numOfQueue: 0,
                numOfSuccess: 0,
                numOfCancel: 0,
                numOfProgress: 0,
                numOfUploadFailed: 0,
                numOfInvalid: 0,
                numOfDeleted: 0,
                numOfInterrupt: 0
            }, this._queue = [], this._map = {}
        }

        var e = a.$, f = c.Status;
        return e.extend(d.prototype, {
            append: function (a) {
                return this._queue.push(a), this._fileAdded(a), this
            }, prepend: function (a) {
                return this._queue.unshift(a), this._fileAdded(a), this
            }, getFile: function (a) {
                return "string" != typeof a ? a : this._map[a]
            }, fetch: function (a) {
                var b, c, d = this._queue.length;
                for (a = a || f.QUEUED, b = 0; d > b; b++) if (c = this._queue[b], a === c.getStatus()) return c;
                return null
            }, sort: function (a) {
                "function" == typeof a && this._queue.sort(a)
            }, getFiles: function () {
                for (var a, b = [].slice.call(arguments, 0), c = [], d = 0, f = this._queue.length; f > d; d++) a = this._queue[d], (!b.length || ~e.inArray(a.getStatus(), b)) && c.push(a);
                return c
            }, removeFile: function (a) {
                var b = this._map[a.id];
                b && (delete this._map[a.id], this._delFile(a), a.destroy(), this.stats.numOfDeleted++)
            }, _fileAdded: function (a) {
                var b = this, c = this._map[a.id];
                c || (this._map[a.id] = a, a.on("statuschange", function (a, c) {
                    b._onFileStatusChange(a, c)
                })), a.setStatus(f.QUEUED)
            }, _delFile: function (a) {
                for (var b = this._queue.length - 1; b >= 0; b--) if (this._queue[b] == a) {
                    this._queue.splice(b, 1);
                    break
                }
            }, _onFileStatusChange: function (a, b) {
                var c = this.stats;
                switch (b) {
                    case f.PROGRESS:
                        c.numOfProgress--;
                        break;
                    case f.QUEUED:
                        c.numOfQueue--;
                        break;
                    case f.ERROR:
                        c.numOfUploadFailed--;
                        break;
                    case f.INVALID:
                        c.numOfInvalid--;
                        break;
                    case f.INTERRUPT:
                        c.numOfInterrupt--
                }
                switch (a) {
                    case f.QUEUED:
                        c.numOfQueue++;
                        break;
                    case f.PROGRESS:
                        c.numOfProgress++;
                        break;
                    case f.ERROR:
                        c.numOfUploadFailed++;
                        break;
                    case f.COMPLETE:
                        c.numOfSuccess++;
                        break;
                    case f.CANCELLED:
                        c.numOfCancel++;
                        break;
                    case f.INVALID:
                        c.numOfInvalid++;
                        break;
                    case f.INTERRUPT:
                        c.numOfInterrupt++
                }
            }
        }), b.installTo(d.prototype), d
    }), b("widgets/queue", ["base", "uploader", "queue", "file", "lib/file", "runtime/client", "widgets/widget"], function (a, b, c, d, e, f) {
        var g = a.$, h = /\.\w+$/, i = d.Status;
        return b.register({
            name: "queue", init: function (b) {
                var d, e, h, i, j, k, l, m = this;
                if (g.isPlainObject(b.accept) && (b.accept = [b.accept]), b.accept) {
                    for (j = [], h = 0, e = b.accept.length; e > h; h++) i = b.accept[h].extensions, i && j.push(i);
                    j.length && (k = "\\." + j.join(",").replace(/,/g, "$|\\.").replace(/\*/g, ".*") + "$"), m.accept = new RegExp(k, "i")
                }
                return m.queue = new c, m.stats = m.queue.stats, "html5" === this.request("predict-runtime-type") ? (d = a.Deferred(), this.placeholder = l = new f("Placeholder"), l.connectRuntime({runtimeOrder: "html5"}, function () {
                    m._ruid = l.getRuid(), d.resolve()
                }), d.promise()) : void 0
            }, _wrapFile: function (a) {
                if (!(a instanceof d)) {
                    if (!(a instanceof e)) {
                        if (!this._ruid) throw new Error("Can't add external files.");
                        a = new e(this._ruid, a)
                    }
                    a = new d(a)
                }
                return a
            }, acceptFile: function (a) {
                var b = !a || !a.size || this.accept && h.exec(a.name) && !this.accept.test(a.name);
                return !b
            }, _addFile: function (a) {
                var b = this;
                return a = b._wrapFile(a), b.owner.trigger("beforeFileQueued", a) ? b.acceptFile(a) ? (b.queue.append(a), b.owner.trigger("fileQueued", a), a) : (b.owner.trigger("error", "Q_TYPE_DENIED", a), void 0) : void 0
            }, getFile: function (a) {
                return this.queue.getFile(a)
            }, addFile: function (a) {
                var b = this;
                a.length || (a = [a]), a = g.map(a, function (a) {
                    return b._addFile(a)
                }), a.length && (b.owner.trigger("filesQueued", a), b.options.auto && setTimeout(function () {
                    b.request("start-upload")
                }, 20))
            }, getStats: function () {
                return this.stats
            }, removeFile: function (a, b) {
                var c = this;
                a = a.id ? a : c.queue.getFile(a), this.request("cancel-file", a), b && this.queue.removeFile(a)
            }, getFiles: function () {
                return this.queue.getFiles.apply(this.queue, arguments)
            }, fetchFile: function () {
                return this.queue.fetch.apply(this.queue, arguments)
            }, retry: function (a, b) {
                var c, d, e, f = this;
                if (a) return a = a.id ? a : f.queue.getFile(a), a.setStatus(i.QUEUED), b || f.request("start-upload"), void 0;
                for (c = f.queue.getFiles(i.ERROR), d = 0, e = c.length; e > d; d++) a = c[d], a.setStatus(i.QUEUED);
                f.request("start-upload")
            }, sortFiles: function () {
                return this.queue.sort.apply(this.queue, arguments)
            }, reset: function () {
                this.owner.trigger("reset"), this.queue = new c, this.stats = this.queue.stats
            }, destroy: function () {
                this.reset(), this.placeholder && this.placeholder.destroy()
            }
        })
    }), b("widgets/runtime", ["uploader", "runtime/runtime", "widgets/widget"], function (a, b) {
        return a.support = function () {
            return b.hasRuntime.apply(b, arguments)
        }, a.register({
            name: "runtime", init: function () {
                if (!this.predictRuntimeType()) throw Error("Runtime Error")
            }, predictRuntimeType: function () {
                var a, c, d = this.options.runtimeOrder || b.orders, e = this.type;
                if (!e) for (d = d.split(/\s*,\s*/g), a = 0, c = d.length; c > a; a++) if (b.hasRuntime(d[a])) {
                    this.type = e = d[a];
                    break
                }
                return e
            }
        })
    }), b("lib/transport", ["base", "runtime/client", "mediator"], function (a, b, c) {
        function d(a) {
            var c = this;
            a = c.options = e.extend(!0, {}, d.options, a || {}), b.call(this, "Transport"), this._blob = null, this._formData = a.formData || {}, this._headers = a.headers || {}, this.on("progress", this._timeout), this.on("load error", function () {
                c.trigger("progress", 1), clearTimeout(c._timer)
            })
        }

        var e = a.$;
        return d.options = {
            server: "",
            method: "POST",
            withCredentials: !1,
            fileVal: "file",
            timeout: 12e4,
            formData: {},
            headers: {},
            sendAsBinary: !1
        }, e.extend(d.prototype, {
            appendBlob: function (a, b, c) {
                var d = this, e = d.options;
                d.getRuid() && d.disconnectRuntime(), d.connectRuntime(b.ruid, function () {
                    d.exec("init")
                }), d._blob = b, e.fileVal = a || e.fileVal, e.filename = c || e.filename
            }, append: function (a, b) {
                "object" == typeof a ? e.extend(this._formData, a) : this._formData[a] = b
            }, setRequestHeader: function (a, b) {
                "object" == typeof a ? e.extend(this._headers, a) : this._headers[a] = b
            }, send: function (a) {
                this.exec("send", a), this._timeout()
            }, abort: function () {
                return clearTimeout(this._timer), this.exec("abort")
            }, destroy: function () {
                this.trigger("destroy"), this.off(), this.exec("destroy"), this.disconnectRuntime()
            }, getResponseHeaders: function () {
                return this.exec("getResponseHeaders")
            }, getResponse: function () {
                return this.exec("getResponse")
            }, getResponseAsJson: function () {
                return this.exec("getResponseAsJson")
            }, getStatus: function () {
                return this.exec("getStatus")
            }, _timeout: function () {
                var a = this, b = a.options.timeout;
                b && (clearTimeout(a._timer), a._timer = setTimeout(function () {
                    a.abort(), a.trigger("error", "timeout")
                }, b))
            }
        }), c.installTo(d.prototype), d
    }), b("widgets/upload", ["base", "uploader", "file", "lib/transport", "widgets/widget"], function (a, b, c, d) {
        function e(a, b) {
            var c, d, e = [], f = a.source, g = f.size, h = b ? Math.ceil(g / b) : 1, i = 0, j = 0;
            for (d = {
                file: a, has: function () {
                    return !!e.length
                }, shift: function () {
                    return e.shift()
                }, unshift: function (a) {
                    e.unshift(a)
                }
            }; h > j;) c = Math.min(b, g - i), e.push({
                file: a,
                start: i,
                end: b ? i + c : g,
                total: g,
                chunks: h,
                chunk: j++,
                cuted: d
            }), i += c;
            return a.blocks = e.concat(), a.remaning = e.length, d
        }

        var f = a.$, g = a.isPromise, h = c.Status;
        f.extend(b.options, {
            prepareNextFile: !1,
            chunked: !1,
            chunkSize: 5242880,
            chunkRetry: 2,
            chunkRetryDelay: 1e3,
            threads: 3,
            formData: {}
        }), b.register({
            name: "upload", init: function () {
                var b = this.owner, c = this;
                this.runing = !1, this.progress = !1, b.on("startUpload", function () {
                    c.progress = !0
                }).on("uploadFinished", function () {
                    c.progress = !1
                }), this.pool = [], this.stack = [], this.pending = [], this.remaning = 0, this.__tick = a.bindFn(this._tick, this), b.on("uploadComplete", function (a) {
                    a.blocks && f.each(a.blocks, function (a, b) {
                        b.transport && (b.transport.abort(), b.transport.destroy()), delete b.transport
                    }), delete a.blocks, delete a.remaning
                })
            }, reset: function () {
                this.request("stop-upload", !0), this.runing = !1, this.pool = [], this.stack = [], this.pending = [], this.remaning = 0, this._trigged = !1, this._promise = null
            }, startUpload: function (b) {
                var c = this;
                if (f.each(c.request("get-files", h.INVALID), function () {
                    c.request("remove-file", this)
                }), b ? (b = b.id ? b : c.request("get-file", b), b.getStatus() === h.INTERRUPT ? (b.setStatus(h.QUEUED), f.each(c.pool, function (a, c) {
                    c.file === b && (c.transport && c.transport.send(), b.setStatus(h.PROGRESS))
                })) : b.getStatus() !== h.PROGRESS && b.setStatus(h.QUEUED)) : f.each(c.request("get-files", [h.INITED]), function () {
                    this.setStatus(h.QUEUED)
                }), c.runing) return c.owner.trigger("startUpload", b), a.nextTick(c.__tick);
                c.runing = !0;
                var d = [];
                b || f.each(c.pool, function (a, b) {
                    var e = b.file;
                    if (e.getStatus() === h.INTERRUPT) {
                        if (c._trigged = !1, d.push(e), b.waiting) return;
                        b.transport ? b.transport.send() : c._doSend(b)
                    }
                }), f.each(d, function () {
                    this.setStatus(h.PROGRESS)
                }), b || f.each(c.request("get-files", h.INTERRUPT), function () {
                    this.setStatus(h.PROGRESS)
                }), c._trigged = !1, a.nextTick(c.__tick), c.owner.trigger("startUpload")
            }, stopUpload: function (b, c) {
                var d = this;
                if (b === !0 && (c = b, b = null), d.runing !== !1) {
                    if (b) {
                        if (b = b.id ? b : d.request("get-file", b), b.getStatus() !== h.PROGRESS && b.getStatus() !== h.QUEUED) return;
                        return b.setStatus(h.INTERRUPT), f.each(d.pool, function (a, e) {
                            e.file === b && (e.transport && e.transport.abort(), c && (d._putback(e), d._popBlock(e)))
                        }), d.owner.trigger("stopUpload", b), a.nextTick(d.__tick)
                    }
                    d.runing = !1, this._promise && this._promise.file && this._promise.file.setStatus(h.INTERRUPT), c && f.each(d.pool, function (a, b) {
                        b.transport && b.transport.abort(), b.file.setStatus(h.INTERRUPT)
                    }), d.owner.trigger("stopUpload")
                }
            }, cancelFile: function (a) {
                a = a.id ? a : this.request("get-file", a), a.blocks && f.each(a.blocks, function (a, b) {
                    var c = b.transport;
                    c && (c.abort(), c.destroy(), delete b.transport)
                }), a.setStatus(h.CANCELLED), this.owner.trigger("fileDequeued", a)
            }, isInProgress: function () {
                return !!this.progress
            }, _getStats: function () {
                return this.request("get-stats")
            }, skipFile: function (a, b) {
                a = a.id ? a : this.request("get-file", a), a.setStatus(b || h.COMPLETE), a.skipped = !0, a.blocks && f.each(a.blocks, function (a, b) {
                    var c = b.transport;
                    c && (c.abort(), c.destroy(), delete b.transport)
                }), this.owner.trigger("uploadSkip", a)
            }, _tick: function () {
                var b, c, d = this, e = d.options;
                return d._promise ? d._promise.always(d.__tick) : (d.pool.length < e.threads && (c = d._nextBlock()) ? (d._trigged = !1, b = function (b) {
                    d._promise = null, b && b.file && d._startSend(b), a.nextTick(d.__tick)
                }, d._promise = g(c) ? c.always(b) : b(c)) : d.remaning || d._getStats().numOfQueue || d._getStats().numOfInterrupt || (d.runing = !1, d._trigged || a.nextTick(function () {
                    d.owner.trigger("uploadFinished")
                }), d._trigged = !0), void 0)
            }, _putback: function (a) {
                var b;
                a.cuted.unshift(a), b = this.stack.indexOf(a.cuted), ~b || (this.remaning++, a.file.remaning++, this.stack.unshift(a.cuted))
            }, _getStack: function () {
                for (var a, b = 0; a = this.stack[b++];) {
                    if (a.has() && a.file.getStatus() === h.PROGRESS) return a;
                    (!a.has() || a.file.getStatus() !== h.PROGRESS && a.file.getStatus() !== h.INTERRUPT) && this.stack.splice(--b, 1)
                }
                return null
            }, _nextBlock: function () {
                var a, b, c, d, f = this, h = f.options;
                return (a = this._getStack()) ? (h.prepareNextFile && !f.pending.length && f._prepareNextFile(), a.shift()) : f.runing ? (!f.pending.length && f._getStats().numOfQueue && f._prepareNextFile(), b = f.pending.shift(), c = function (b) {
                    return b ? (a = e(b, h.chunked ? h.chunkSize : 0), f.stack.push(a), a.shift()) : null
                }, g(b) ? (d = b.file, b = b[b.pipe ? "pipe" : "then"](c), b.file = d, b) : c(b)) : void 0
            }, _prepareNextFile: function () {
                var a, b = this, c = b.request("fetch-file"), d = b.pending;
                c && (a = b.request("before-send-file", c, function () {
                    return c.getStatus() === h.PROGRESS || c.getStatus() === h.INTERRUPT ? c : b._finishFile(c)
                }), b.owner.trigger("uploadStart", c), c.setStatus(h.PROGRESS), a.file = c, a.done(function () {
                    var b = f.inArray(a, d);
                    ~b && d.splice(b, 1, c)
                }), a.fail(function (a) {
                    c.setStatus(h.ERROR, a), b.owner.trigger("uploadError", c, a), b.owner.trigger("uploadComplete", c)
                }), d.push(a))
            }, _popBlock: function (a) {
                var b = f.inArray(a, this.pool);
                this.pool.splice(b, 1), a.file.remaning--, this.remaning--
            }, _startSend: function (b) {
                var c, d = this, e = b.file;
                return e.getStatus() !== h.PROGRESS ? (e.getStatus() === h.INTERRUPT && d._putback(b), void 0) : (d.pool.push(b), d.remaning++, b.blob = 1 === b.chunks ? e.source : e.source.slice(b.start, b.end), b.waiting = c = d.request("before-send", b, function () {
                    delete b.waiting, e.getStatus() === h.PROGRESS ? d._doSend(b) : b.file.getStatus() !== h.INTERRUPT && d._popBlock(b), a.nextTick(d.__tick)
                }), c.fail(function () {
                    delete b.waiting, 1 === e.remaning ? d._finishFile(e).always(function () {
                        b.percentage = 1, d._popBlock(b), d.owner.trigger("uploadComplete", e), a.nextTick(d.__tick)
                    }) : (b.percentage = 1, d.updateFileProgress(e), d._popBlock(b), a.nextTick(d.__tick))
                }), void 0)
            }, _doSend: function (b) {
                var c, e, g = this, i = g.owner, j = f.extend({}, g.options, b.options), k = b.file, l = new d(j),
                    m = f.extend({}, j.formData), n = f.extend({}, j.headers);
                b.transport = l, l.on("destroy", function () {
                    delete b.transport, g._popBlock(b), a.nextTick(g.__tick)
                }), l.on("progress", function (a) {
                    b.percentage = a, g.updateFileProgress(k)
                }), c = function (a) {
                    var c;
                    return e = l.getResponseAsJson() || {}, e._raw = l.getResponse(), e._headers = l.getResponseHeaders(), b.response = e, c = function (b) {
                        a = b
                    }, i.trigger("uploadAccept", b, e, c) || (a = a || "server"), a
                }, l.on("error", function (a, d) {
                    var e, f, m = a.split("|");
                    a = m[0], e = parseFloat(m[1]), f = m[2], b.retried = b.retried || 0, b.chunks > 1 && ~"http,abort,server".indexOf(a.replace(/-.*/, "")) && b.retried < j.chunkRetry ? (b.retried++, g.retryTimer = setTimeout(function () {
                        l.send()
                    }, j.chunkRetryDelay || 1e3)) : (d || "server" !== a || (a = c(a)), k.setStatus(h.ERROR, a), i.trigger("uploadError", k, a, e, f), i.trigger("uploadComplete", k))
                }), l.on("load", function () {
                    var a;
                    return (a = c()) ? (l.trigger("error", a, !0), void 0) : (1 === k.remaning ? g._finishFile(k, e) : l.destroy(), void 0)
                }), m = f.extend(m, {
                    id: k.id,
                    name: k.name,
                    type: k.type,
                    lastModifiedDate: k.lastModifiedDate,
                    size: k.size
                }), b.chunks > 1 && f.extend(m, {
                    chunks: b.chunks,
                    chunk: b.chunk
                }), i.trigger("uploadBeforeSend", b, m, n), l.appendBlob(j.fileVal, b.blob, k.name), l.append(m), l.setRequestHeader(n), l.send()
            }, _finishFile: function (a, b, c) {
                var d = this.owner;
                return d.request("after-send-file", arguments, function () {
                    a.setStatus(h.COMPLETE), d.trigger("uploadSuccess", a, b, c)
                }).fail(function (b) {
                    a.getStatus() === h.PROGRESS && a.setStatus(h.ERROR, b), d.trigger("uploadError", a, b)
                }).always(function () {
                    d.trigger("uploadComplete", a)
                })
            }, updateFileProgress: function (a) {
                var b = 0, c = 0;
                a.blocks && (f.each(a.blocks, function (a, b) {
                    c += (b.percentage || 0) * (b.end - b.start)
                }), b = c / a.size, this.owner.trigger("uploadProgress", a, b || 0))
            }, destroy: function () {
                clearTimeout(this.retryTimer)
            }
        })
    }), b("widgets/validator", ["base", "uploader", "file", "widgets/widget"], function (a, b, c) {
        var d, e = a.$, f = {};
        return d = {
            addValidator: function (a, b) {
                f[a] = b
            }, removeValidator: function (a) {
                delete f[a]
            }
        }, b.register({
            name: "validator", init: function () {
                var b = this;
                a.nextTick(function () {
                    e.each(f, function () {
                        this.call(b.owner)
                    })
                })
            }
        }), d.addValidator("fileNumLimit", function () {
            var a = this, b = a.options, c = 0, d = parseInt(b.fileNumLimit, 10), e = !0;
            d && (a.on("beforeFileQueued", function (a) {
                return this.trigger("beforeFileQueuedCheckfileNumLimit", a, c) ? (c >= d && e && (e = !1, this.trigger("error", "Q_EXCEED_NUM_LIMIT", d, a), setTimeout(function () {
                    e = !0
                }, 1)), c >= d ? !1 : !0) : !1
            }), a.on("fileQueued", function () {
                c++
            }), a.on("fileDequeued", function () {
                c--
            }), a.on("reset", function () {
                c = 0
            }))
        }), d.addValidator("fileSizeLimit", function () {
            var a = this, b = a.options, c = 0, d = parseInt(b.fileSizeLimit, 10), e = !0;
            d && (a.on("beforeFileQueued", function (a) {
                var b = c + a.size > d;
                return b && e && (e = !1, this.trigger("error", "Q_EXCEED_SIZE_LIMIT", d, a), setTimeout(function () {
                    e = !0
                }, 1)), b ? !1 : !0
            }), a.on("fileQueued", function (a) {
                c += a.size
            }), a.on("fileDequeued", function (a) {
                c -= a.size
            }), a.on("reset", function () {
                c = 0
            }))
        }), d.addValidator("fileSingleSizeLimit", function () {
            var a = this, b = a.options, d = b.fileSingleSizeLimit;
            d && a.on("beforeFileQueued", function (a) {
                return a.size > d ? (a.setStatus(c.Status.INVALID, "exceed_size"), this.trigger("error", "F_EXCEED_SIZE", d, a), !1) : void 0
            })
        }), d.addValidator("duplicate", function () {
            function a(a) {
                for (var b, c = 0, d = 0, e = a.length; e > d; d++) b = a.charCodeAt(d), c = b + (c << 6) + (c << 16) - c;
                return c
            }

            var b = this, c = b.options, d = {};
            c.duplicate || (b.on("beforeFileQueued", function (b) {
                var c = b.__hash || (b.__hash = a(b.name + b.size + b.lastModifiedDate));
                return d[c] ? (this.trigger("error", "F_DUPLICATE", b), !1) : void 0
            }), b.on("fileQueued", function (a) {
                var b = a.__hash;
                b && (d[b] = !0)
            }), b.on("fileDequeued", function (a) {
                var b = a.__hash;
                b && delete d[b]
            }), b.on("reset", function () {
                d = {}
            }))
        }), d
    }), b("lib/md5", ["runtime/client", "mediator"], function (a, b) {
        function c() {
            a.call(this, "Md5")
        }

        return b.installTo(c.prototype), c.prototype.loadFromBlob = function (a) {
            var b = this;
            b.getRuid() && b.disconnectRuntime(), b.connectRuntime(a.ruid, function () {
                b.exec("init"), b.exec("loadFromBlob", a)
            })
        }, c.prototype.getResult = function () {
            return this.exec("getResult")
        }, c
    }), b("widgets/md5", ["base", "uploader", "lib/md5", "lib/blob", "widgets/widget"], function (a, b, c, d) {
        return b.register({
            name: "md5", md5File: function (b, e, f) {
                var g = new c, h = a.Deferred(), i = b instanceof d ? b : this.request("get-file", b).source;
                return g.on("progress load", function (a) {
                    a = a || {}, h.notify(a.total ? a.loaded / a.total : 1)
                }), g.on("complete", function () {
                    h.resolve(g.getResult())
                }), g.on("error", function (a) {
                    h.reject(a)
                }), arguments.length > 1 && (e = e || 0, f = f || 0, 0 > e && (e = i.size + e), 0 > f && (f = i.size + f), f = Math.min(f, i.size), i = i.slice(e, f)), g.loadFromBlob(i), h.promise()
            }
        })
    }), b("runtime/compbase", [], function () {
        function a(a, b) {
            this.owner = a, this.options = a.options, this.getRuntime = function () {
                return b
            }, this.getRuid = function () {
                return b.uid
            }, this.trigger = function () {
                return a.trigger.apply(a, arguments)
            }
        }

        return a
    }), b("runtime/html5/runtime", ["base", "runtime/runtime", "runtime/compbase"], function (b, c, d) {
        function e() {
            var a = {}, d = this, e = this.destroy;
            c.apply(d, arguments), d.type = f, d.exec = function (c, e) {
                var f, h = this, i = h.uid, j = b.slice(arguments, 2);
                return g[c] && (f = a[i] = a[i] || new g[c](h, d), f[e]) ? f[e].apply(f, j) : void 0
            }, d.destroy = function () {
                return e && e.apply(this, arguments)
            }
        }

        var f = "html5", g = {};
        return b.inherits(c, {
            constructor: e, init: function () {
                var a = this;
                setTimeout(function () {
                    a.trigger("ready")
                }, 1)
            }
        }), e.register = function (a, c) {
            var e = g[a] = b.inherits(d, c);
            return e
        }, a.Blob && a.FileReader && a.DataView && c.addRuntime(f, e), e
    }), b("runtime/html5/blob", ["runtime/html5/runtime", "lib/blob"], function (a, b) {
        return a.register("Blob", {
            slice: function (a, c) {
                var d = this.owner.source, e = d.slice || d.webkitSlice || d.mozSlice;
                return d = e.call(d, a, c), new b(this.getRuid(), d)
            }
        })
    }), b("runtime/html5/dnd", ["base", "runtime/html5/runtime", "lib/file"], function (a, b, c) {
        var d = a.$, e = "webuploader-dnd-";
        return b.register("DragAndDrop", {
            init: function () {
                var b = this.elem = this.options.container;
                this.dragEnterHandler = a.bindFn(this._dragEnterHandler, this), this.dragOverHandler = a.bindFn(this._dragOverHandler, this), this.dragLeaveHandler = a.bindFn(this._dragLeaveHandler, this), this.dropHandler = a.bindFn(this._dropHandler, this), this.dndOver = !1, b.on("dragenter", this.dragEnterHandler), b.on("dragover", this.dragOverHandler), b.on("dragleave", this.dragLeaveHandler), b.on("drop", this.dropHandler), this.options.disableGlobalDnd && (d(document).on("dragover", this.dragOverHandler), d(document).on("drop", this.dropHandler))
            }, _dragEnterHandler: function (a) {
                var b, c = this, d = c._denied || !1;
                return a = a.originalEvent || a, c.dndOver || (c.dndOver = !0, b = a.dataTransfer.items, b && b.length && (c._denied = d = !c.trigger("accept", b)), c.elem.addClass(e + "over"), c.elem[d ? "addClass" : "removeClass"](e + "denied")), a.dataTransfer.dropEffect = d ? "none" : "copy", !1
            }, _dragOverHandler: function (a) {
                var b = this.elem.parent().get(0);
                return b && !d.contains(b, a.currentTarget) ? !1 : (clearTimeout(this._leaveTimer), this._dragEnterHandler.call(this, a), !1)
            }, _dragLeaveHandler: function () {
                var a, b = this;
                return a = function () {
                    b.dndOver = !1, b.elem.removeClass(e + "over " + e + "denied")
                }, clearTimeout(b._leaveTimer), b._leaveTimer = setTimeout(a, 100), !1
            }, _dropHandler: function (a) {
                var b, f, g = this, h = g.getRuid(), i = g.elem.parent().get(0);
                if (i && !d.contains(i, a.currentTarget)) return !1;
                a = a.originalEvent || a, b = a.dataTransfer;
                try {
                    f = b.getData("text/html")
                } catch (j) {
                }
                return g.dndOver = !1, g.elem.removeClass(e + "over"), b && !f ? (g._getTansferFiles(b, function (a) {
                    g.trigger("drop", d.map(a, function (a) {
                        return new c(h, a)
                    }))
                }), !1) : void 0
            }, _getTansferFiles: function (b, c) {
                var d, e, f, g, h, i, j, k = [], l = [];
                for (d = b.items, e = b.files, j = !(!d || !d[0].webkitGetAsEntry), h = 0, i = e.length; i > h; h++) f = e[h], g = d && d[h], j && g.webkitGetAsEntry().isDirectory ? l.push(this._traverseDirectoryTree(g.webkitGetAsEntry(), k)) : k.push(f);
                a.when.apply(a, l).done(function () {
                    k.length && c(k)
                })
            }, _traverseDirectoryTree: function (b, c) {
                var d = a.Deferred(), e = this;
                return b.isFile ? b.file(function (a) {
                    c.push(a), d.resolve()
                }) : b.isDirectory && b.createReader().readEntries(function (b) {
                    var f, g = b.length, h = [], i = [];
                    for (f = 0; g > f; f++) h.push(e._traverseDirectoryTree(b[f], i));
                    a.when.apply(a, h).then(function () {
                        c.push.apply(c, i), d.resolve()
                    }, d.reject)
                }), d.promise()
            }, destroy: function () {
                var a = this.elem;
                a && (a.off("dragenter", this.dragEnterHandler), a.off("dragover", this.dragOverHandler), a.off("dragleave", this.dragLeaveHandler), a.off("drop", this.dropHandler), this.options.disableGlobalDnd && (d(document).off("dragover", this.dragOverHandler), d(document).off("drop", this.dropHandler)))
            }
        })
    }), b("runtime/html5/filepaste", ["base", "runtime/html5/runtime", "lib/file"], function (a, b, c) {
        return b.register("FilePaste", {
            init: function () {
                var b, c, d, e, f = this.options, g = this.elem = f.container, h = ".*";
                if (f.accept) {
                    for (b = [], c = 0, d = f.accept.length; d > c; c++) e = f.accept[c].mimeTypes, e && b.push(e);
                    b.length && (h = b.join(","), h = h.replace(/,/g, "|").replace(/\*/g, ".*"))
                }
                this.accept = h = new RegExp(h, "i"), this.hander = a.bindFn(this._pasteHander, this), g.on("paste", this.hander)
            }, _pasteHander: function (a) {
                var b, d, e, f, g, h = [], i = this.getRuid();
                for (a = a.originalEvent || a, b = a.clipboardData.items, f = 0, g = b.length; g > f; f++) d = b[f], "file" === d.kind && (e = d.getAsFile()) && h.push(new c(i, e));
                h.length && (a.preventDefault(), a.stopPropagation(), this.trigger("paste", h))
            }, destroy: function () {
                this.elem.off("paste", this.hander)
            }
        })
    }), b("runtime/html5/filepicker", ["base", "runtime/html5/runtime"], function (a, b) {
        var c = a.$;
        return b.register("FilePicker", {
            init: function () {
                var a, b, d, e, f, g = this.getRuntime().getContainer(), h = this, i = h.owner, j = h.options,
                    k = this.label = c(document.createElement("label")),
                    l = this.input = c(document.createElement("input"));
                if (l.attr("type", "file"), l.attr("capture", "camera"), l.attr("name", j.name), l.addClass("webuploader-element-invisible"), k.on("click", function (a) {
                    l.trigger("click"), a.stopPropagation(), i.trigger("dialogopen")
                }), k.css({
                    opacity: 0,
                    width: "100%",
                    height: "100%",
                    display: "block",
                    cursor: "pointer",
                    background: "#ffffff"
                }), j.multiple && l.attr("multiple", "multiple"), j.accept && j.accept.length > 0) {
                    for (a = [], b = 0, d = j.accept.length; d > b; b++) a.push(j.accept[b].mimeTypes);
                    l.attr("accept", a.join(","))
                }
                g.append(l), g.append(k), e = function (a) {
                    i.trigger(a.type)
                }, f = function (a) {
                    var b;
                    return 0 === a.target.files.length ? !1 : (h.files = a.target.files, b = this.cloneNode(!0), b.value = null, this.parentNode.replaceChild(b, this), l.off(), l = c(b).on("change", f).on("mouseenter mouseleave", e), i.trigger("change"), void 0)
                }, l.on("change", f), k.on("mouseenter mouseleave", e)
            }, getFiles: function () {
                return this.files
            }, destroy: function () {
                this.input.off(), this.label.off()
            }
        })
    }), b("runtime/html5/util", ["base"], function (b) {
        var c = a.createObjectURL && a || a.URL && URL.revokeObjectURL && URL || a.webkitURL, d = b.noop, e = d;
        return c && (d = function () {
            return c.createObjectURL.apply(c, arguments)
        }, e = function () {
            return c.revokeObjectURL.apply(c, arguments)
        }), {
            createObjectURL: d, revokeObjectURL: e, dataURL2Blob: function (a) {
                var b, c, d, e, f, g;
                for (g = a.split(","), b = ~g[0].indexOf("base64") ? atob(g[1]) : decodeURIComponent(g[1]), d = new ArrayBuffer(b.length), c = new Uint8Array(d), e = 0; e < b.length; e++) c[e] = b.charCodeAt(e);
                return f = g[0].split(":")[1].split(";")[0], this.arrayBufferToBlob(d, f)
            }, dataURL2ArrayBuffer: function (a) {
                var b, c, d, e;
                for (e = a.split(","), b = ~e[0].indexOf("base64") ? atob(e[1]) : decodeURIComponent(e[1]), c = new Uint8Array(b.length), d = 0; d < b.length; d++) c[d] = b.charCodeAt(d);
                return c.buffer
            }, arrayBufferToBlob: function (b, c) {
                var d, e = a.BlobBuilder || a.WebKitBlobBuilder;
                return e ? (d = new e, d.append(b), d.getBlob(c)) : new Blob([b], c ? {type: c} : {})
            }, canvasToDataUrl: function (a, b, c) {
                return a.toDataURL(b, c / 100)
            }, parseMeta: function (a, b) {
                b(!1, {})
            }, updateImageHead: function (a) {
                return a
            }
        }
    }), b("runtime/html5/imagemeta", ["runtime/html5/util"], function (a) {
        var b;
        return b = {
            parsers: {65505: []}, maxMetaDataSize: 262144, parse: function (a, b) {
                var c = this, d = new FileReader;
                d.onload = function () {
                    b(!1, c._parse(this.result)), d = d.onload = d.onerror = null
                }, d.onerror = function (a) {
                    b(a.message), d = d.onload = d.onerror = null
                }, a = a.slice(0, c.maxMetaDataSize), d.readAsArrayBuffer(a.getSource())
            }, _parse: function (a, c) {
                if (!(a.byteLength < 6)) {
                    var d, e, f, g, h = new DataView(a), i = 2, j = h.byteLength - 4, k = i, l = {};
                    if (65496 === h.getUint16(0)) {
                        for (; j > i && (d = h.getUint16(i), d >= 65504 && 65519 >= d || 65534 === d) && (e = h.getUint16(i + 2) + 2, !(i + e > h.byteLength));) {
                            if (f = b.parsers[d], !c && f) for (g = 0; g < f.length; g += 1) f[g].call(b, h, i, e, l);
                            i += e, k = i
                        }
                        k > 6 && (l.imageHead = a.slice ? a.slice(2, k) : new Uint8Array(a).subarray(2, k))
                    }
                    return l
                }
            }, updateImageHead: function (a, b) {
                var c, d, e, f = this._parse(a, !0);
                return e = 2, f.imageHead && (e = 2 + f.imageHead.byteLength), d = a.slice ? a.slice(e) : new Uint8Array(a).subarray(e), c = new Uint8Array(b.byteLength + 2 + d.byteLength), c[0] = 255, c[1] = 216, c.set(new Uint8Array(b), 2), c.set(new Uint8Array(d), b.byteLength + 2), c.buffer
            }
        }, a.parseMeta = function () {
            return b.parse.apply(b, arguments)
        }, a.updateImageHead = function () {
            return b.updateImageHead.apply(b, arguments)
        }, b
    }), b("runtime/html5/imagemeta/exif", ["base", "runtime/html5/imagemeta"], function (a, b) {
        var c = {};
        return c.ExifMap = function () {
            return this
        }, c.ExifMap.prototype.map = {Orientation: 274}, c.ExifMap.prototype.get = function (a) {
            return this[a] || this[this.map[a]]
        }, c.exifTagTypes = {
            1: {
                getValue: function (a, b) {
                    return a.getUint8(b)
                }, size: 1
            }, 2: {
                getValue: function (a, b) {
                    return String.fromCharCode(a.getUint8(b))
                }, size: 1, ascii: !0
            }, 3: {
                getValue: function (a, b, c) {
                    return a.getUint16(b, c)
                }, size: 2
            }, 4: {
                getValue: function (a, b, c) {
                    return a.getUint32(b, c)
                }, size: 4
            }, 5: {
                getValue: function (a, b, c) {
                    return a.getUint32(b, c) / a.getUint32(b + 4, c)
                }, size: 8
            }, 9: {
                getValue: function (a, b, c) {
                    return a.getInt32(b, c)
                }, size: 4
            }, 10: {
                getValue: function (a, b, c) {
                    return a.getInt32(b, c) / a.getInt32(b + 4, c)
                }, size: 8
            }
        }, c.exifTagTypes[7] = c.exifTagTypes[1], c.getExifValue = function (b, d, e, f, g, h) {
            var i, j, k, l, m, n, o = c.exifTagTypes[f];
            if (!o) return a.log("Invalid Exif data: Invalid tag type."), void 0;
            if (i = o.size * g, j = i > 4 ? d + b.getUint32(e + 8, h) : e + 8, j + i > b.byteLength) return a.log("Invalid Exif data: Invalid data offset."), void 0;
            if (1 === g) return o.getValue(b, j, h);
            for (k = [], l = 0; g > l; l += 1) k[l] = o.getValue(b, j + l * o.size, h);
            if (o.ascii) {
                for (m = "", l = 0; l < k.length && (n = k[l], "\0" !== n); l += 1) m += n;
                return m
            }
            return k
        }, c.parseExifTag = function (a, b, d, e, f) {
            var g = a.getUint16(d, e);
            f.exif[g] = c.getExifValue(a, b, d, a.getUint16(d + 2, e), a.getUint32(d + 4, e), e)
        }, c.parseExifTags = function (b, c, d, e, f) {
            var g, h, i;
            if (d + 6 > b.byteLength) return a.log("Invalid Exif data: Invalid directory offset."), void 0;
            if (g = b.getUint16(d, e), h = d + 2 + 12 * g, h + 4 > b.byteLength) return a.log("Invalid Exif data: Invalid directory size."), void 0;
            for (i = 0; g > i; i += 1) this.parseExifTag(b, c, d + 2 + 12 * i, e, f);
            return b.getUint32(h, e)
        }, c.parseExifData = function (b, d, e, f) {
            var g, h, i = d + 10;
            if (1165519206 === b.getUint32(d + 4)) {
                if (i + 8 > b.byteLength) return a.log("Invalid Exif data: Invalid segment size."), void 0;
                if (0 !== b.getUint16(d + 8)) return a.log("Invalid Exif data: Missing byte alignment offset."), void 0;
                switch (b.getUint16(i)) {
                    case 18761:
                        g = !0;
                        break;
                    case 19789:
                        g = !1;
                        break;
                    default:
                        return a.log("Invalid Exif data: Invalid byte alignment marker."), void 0
                }
                if (42 !== b.getUint16(i + 2, g)) return a.log("Invalid Exif data: Missing TIFF marker."), void 0;
                h = b.getUint32(i + 4, g), f.exif = new c.ExifMap, h = c.parseExifTags(b, i, i + h, g, f)
            }
        }, b.parsers[65505].push(c.parseExifData), c
    }), b("runtime/html5/jpegencoder", [], function () {
        function a(a) {
            function b(a) {
                for (var b = [16, 11, 10, 16, 24, 40, 51, 61, 12, 12, 14, 19, 26, 58, 60, 55, 14, 13, 16, 24, 40, 57, 69, 56, 14, 17, 22, 29, 51, 87, 80, 62, 18, 22, 37, 56, 68, 109, 103, 77, 24, 35, 55, 64, 81, 104, 113, 92, 49, 64, 78, 87, 103, 121, 120, 101, 72, 92, 95, 98, 112, 100, 103, 99], c = 0; 64 > c; c++) {
                    var d = y((b[c] * a + 50) / 100);
                    1 > d ? d = 1 : d > 255 && (d = 255), z[P[c]] = d
                }
                for (var e = [17, 18, 24, 47, 99, 99, 99, 99, 18, 21, 26, 66, 99, 99, 99, 99, 24, 26, 56, 99, 99, 99, 99, 99, 47, 66, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99], f = 0; 64 > f; f++) {
                    var g = y((e[f] * a + 50) / 100);
                    1 > g ? g = 1 : g > 255 && (g = 255), A[P[f]] = g
                }
                for (var h = [1, 1.387039845, 1.306562965, 1.175875602, 1, .785694958, .5411961, .275899379], i = 0, j = 0; 8 > j; j++) for (var k = 0; 8 > k; k++) B[i] = 1 / (8 * z[P[i]] * h[j] * h[k]), C[i] = 1 / (8 * A[P[i]] * h[j] * h[k]), i++
            }

            function c(a, b) {
                for (var c = 0, d = 0, e = new Array, f = 1; 16 >= f; f++) {
                    for (var g = 1; g <= a[f]; g++) e[b[d]] = [], e[b[d]][0] = c, e[b[d]][1] = f, d++, c++;
                    c *= 2
                }
                return e
            }

            function d() {
                t = c(Q, R), u = c(U, V), v = c(S, T), w = c(W, X)
            }

            function e() {
                for (var a = 1, b = 2, c = 1; 15 >= c; c++) {
                    for (var d = a; b > d; d++) E[32767 + d] = c, D[32767 + d] = [], D[32767 + d][1] = c, D[32767 + d][0] = d;
                    for (var e = -(b - 1); -a >= e; e++) E[32767 + e] = c, D[32767 + e] = [], D[32767 + e][1] = c, D[32767 + e][0] = b - 1 + e;
                    a <<= 1, b <<= 1
                }
            }

            function f() {
                for (var a = 0; 256 > a; a++) O[a] = 19595 * a, O[a + 256 >> 0] = 38470 * a, O[a + 512 >> 0] = 7471 * a + 32768, O[a + 768 >> 0] = -11059 * a, O[a + 1024 >> 0] = -21709 * a, O[a + 1280 >> 0] = 32768 * a + 8421375, O[a + 1536 >> 0] = -27439 * a, O[a + 1792 >> 0] = -5329 * a
            }

            function g(a) {
                for (var b = a[0], c = a[1] - 1; c >= 0;) b & 1 << c && (I |= 1 << J), c--, J--, 0 > J && (255 == I ? (h(255), h(0)) : h(I), J = 7, I = 0)
            }

            function h(a) {
                H.push(N[a])
            }

            function i(a) {
                h(255 & a >> 8), h(255 & a)
            }

            function j(a, b) {
                var c, d, e, f, g, h, i, j, k, l = 0, m = 8, n = 64;
                for (k = 0; m > k; ++k) {
                    c = a[l], d = a[l + 1], e = a[l + 2], f = a[l + 3], g = a[l + 4], h = a[l + 5], i = a[l + 6], j = a[l + 7];
                    var o = c + j, p = c - j, q = d + i, r = d - i, s = e + h, t = e - h, u = f + g, v = f - g,
                        w = o + u, x = o - u, y = q + s, z = q - s;
                    a[l] = w + y, a[l + 4] = w - y;
                    var A = .707106781 * (z + x);
                    a[l + 2] = x + A, a[l + 6] = x - A, w = v + t, y = t + r, z = r + p;
                    var B = .382683433 * (w - z), C = .5411961 * w + B, D = 1.306562965 * z + B, E = .707106781 * y,
                        G = p + E, H = p - E;
                    a[l + 5] = H + C, a[l + 3] = H - C, a[l + 1] = G + D, a[l + 7] = G - D, l += 8
                }
                for (l = 0, k = 0; m > k; ++k) {
                    c = a[l], d = a[l + 8], e = a[l + 16], f = a[l + 24], g = a[l + 32], h = a[l + 40], i = a[l + 48], j = a[l + 56];
                    var I = c + j, J = c - j, K = d + i, L = d - i, M = e + h, N = e - h, O = f + g, P = f - g,
                        Q = I + O, R = I - O, S = K + M, T = K - M;
                    a[l] = Q + S, a[l + 32] = Q - S;
                    var U = .707106781 * (T + R);
                    a[l + 16] = R + U, a[l + 48] = R - U, Q = P + N, S = N + L, T = L + J;
                    var V = .382683433 * (Q - T), W = .5411961 * Q + V, X = 1.306562965 * T + V, Y = .707106781 * S,
                        Z = J + Y, $ = J - Y;
                    a[l + 40] = $ + W, a[l + 24] = $ - W, a[l + 8] = Z + X, a[l + 56] = Z - X, l++
                }
                var _;
                for (k = 0; n > k; ++k) _ = a[k] * b[k], F[k] = _ > 0 ? 0 | _ + .5 : 0 | _ - .5;
                return F
            }

            function k() {
                i(65504), i(16), h(74), h(70), h(73), h(70), h(0), h(1), h(1), h(0), i(1), i(1), h(0), h(0)
            }

            function l(a, b) {
                i(65472), i(17), h(8), i(b), i(a), h(3), h(1), h(17), h(0), h(2), h(17), h(1), h(3), h(17), h(1)
            }

            function m() {
                i(65499), i(132), h(0);
                for (var a = 0; 64 > a; a++) h(z[a]);
                h(1);
                for (var b = 0; 64 > b; b++) h(A[b])
            }

            function n() {
                i(65476), i(418), h(0);
                for (var a = 0; 16 > a; a++) h(Q[a + 1]);
                for (var b = 0; 11 >= b; b++) h(R[b]);
                h(16);
                for (var c = 0; 16 > c; c++) h(S[c + 1]);
                for (var d = 0; 161 >= d; d++) h(T[d]);
                h(1);
                for (var e = 0; 16 > e; e++) h(U[e + 1]);
                for (var f = 0; 11 >= f; f++) h(V[f]);
                h(17);
                for (var g = 0; 16 > g; g++) h(W[g + 1]);
                for (var j = 0; 161 >= j; j++) h(X[j])
            }

            function o() {
                i(65498), i(12), h(3), h(1), h(0), h(2), h(17), h(3), h(17), h(0), h(63), h(0)
            }

            function p(a, b, c, d, e) {
                for (var f, h = e[0], i = e[240], k = 16, l = 63, m = 64, n = j(a, b), o = 0; m > o; ++o) G[P[o]] = n[o];
                var p = G[0] - c;
                c = G[0], 0 == p ? g(d[0]) : (f = 32767 + p, g(d[E[f]]), g(D[f]));
                for (var q = 63; q > 0 && 0 == G[q]; q--) ;
                if (0 == q) return g(h), c;
                for (var r, s = 1; q >= s;) {
                    for (var t = s; 0 == G[s] && q >= s; ++s) ;
                    var u = s - t;
                    if (u >= k) {
                        r = u >> 4;
                        for (var v = 1; r >= v; ++v) g(i);
                        u = 15 & u
                    }
                    f = 32767 + G[s], g(e[(u << 4) + E[f]]), g(D[f]), s++
                }
                return q != l && g(h), c
            }

            function q() {
                for (var a = String.fromCharCode, b = 0; 256 > b; b++) N[b] = a(b)
            }

            function r(a) {
                if (0 >= a && (a = 1), a > 100 && (a = 100), x != a) {
                    var c = 0;
                    c = 50 > a ? Math.floor(5e3 / a) : Math.floor(200 - 2 * a), b(c), x = a
                }
            }

            function s() {
                a || (a = 50), q(), d(), e(), f(), r(a)
            }

            Math.round;
            var t, u, v, w, x, y = Math.floor, z = new Array(64), A = new Array(64), B = new Array(64),
                C = new Array(64), D = new Array(65535), E = new Array(65535), F = new Array(64), G = new Array(64),
                H = [], I = 0, J = 7, K = new Array(64), L = new Array(64), M = new Array(64), N = new Array(256),
                O = new Array(2048),
                P = [0, 1, 5, 6, 14, 15, 27, 28, 2, 4, 7, 13, 16, 26, 29, 42, 3, 8, 12, 17, 25, 30, 41, 43, 9, 11, 18, 24, 31, 40, 44, 53, 10, 19, 23, 32, 39, 45, 52, 54, 20, 22, 33, 38, 46, 51, 55, 60, 21, 34, 37, 47, 50, 56, 59, 61, 35, 36, 48, 49, 57, 58, 62, 63],
                Q = [0, 0, 1, 5, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0], R = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                S = [0, 0, 2, 1, 3, 3, 2, 4, 3, 5, 5, 4, 4, 0, 0, 1, 125],
                T = [1, 2, 3, 0, 4, 17, 5, 18, 33, 49, 65, 6, 19, 81, 97, 7, 34, 113, 20, 50, 129, 145, 161, 8, 35, 66, 177, 193, 21, 82, 209, 240, 36, 51, 98, 114, 130, 9, 10, 22, 23, 24, 25, 26, 37, 38, 39, 40, 41, 42, 52, 53, 54, 55, 56, 57, 58, 67, 68, 69, 70, 71, 72, 73, 74, 83, 84, 85, 86, 87, 88, 89, 90, 99, 100, 101, 102, 103, 104, 105, 106, 115, 116, 117, 118, 119, 120, 121, 122, 131, 132, 133, 134, 135, 136, 137, 138, 146, 147, 148, 149, 150, 151, 152, 153, 154, 162, 163, 164, 165, 166, 167, 168, 169, 170, 178, 179, 180, 181, 182, 183, 184, 185, 186, 194, 195, 196, 197, 198, 199, 200, 201, 202, 210, 211, 212, 213, 214, 215, 216, 217, 218, 225, 226, 227, 228, 229, 230, 231, 232, 233, 234, 241, 242, 243, 244, 245, 246, 247, 248, 249, 250],
                U = [0, 0, 3, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0], V = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                W = [0, 0, 2, 1, 2, 4, 4, 3, 4, 7, 5, 4, 4, 0, 1, 2, 119],
                X = [0, 1, 2, 3, 17, 4, 5, 33, 49, 6, 18, 65, 81, 7, 97, 113, 19, 34, 50, 129, 8, 20, 66, 145, 161, 177, 193, 9, 35, 51, 82, 240, 21, 98, 114, 209, 10, 22, 36, 52, 225, 37, 241, 23, 24, 25, 26, 38, 39, 40, 41, 42, 53, 54, 55, 56, 57, 58, 67, 68, 69, 70, 71, 72, 73, 74, 83, 84, 85, 86, 87, 88, 89, 90, 99, 100, 101, 102, 103, 104, 105, 106, 115, 116, 117, 118, 119, 120, 121, 122, 130, 131, 132, 133, 134, 135, 136, 137, 138, 146, 147, 148, 149, 150, 151, 152, 153, 154, 162, 163, 164, 165, 166, 167, 168, 169, 170, 178, 179, 180, 181, 182, 183, 184, 185, 186, 194, 195, 196, 197, 198, 199, 200, 201, 202, 210, 211, 212, 213, 214, 215, 216, 217, 218, 226, 227, 228, 229, 230, 231, 232, 233, 234, 242, 243, 244, 245, 246, 247, 248, 249, 250];
            this.encode = function (a, b) {
                b && r(b), H = new Array, I = 0, J = 7, i(65496), k(), m(), l(a.width, a.height), n(), o();
                var c = 0, d = 0, e = 0;
                I = 0, J = 7, this.encode.displayName = "_encode_";
                for (var f, h, j, q, s, x, y, z, A, D = a.data, E = a.width, F = a.height, G = 4 * E, N = 0; F > N;) {
                    for (f = 0; G > f;) {
                        for (s = G * N + f, x = s, y = -1, z = 0, A = 0; 64 > A; A++) z = A >> 3, y = 4 * (7 & A), x = s + z * G + y, N + z >= F && (x -= G * (N + 1 + z - F)), f + y >= G && (x -= f + y - G + 4), h = D[x++], j = D[x++], q = D[x++], K[A] = (O[h] + O[j + 256 >> 0] + O[q + 512 >> 0] >> 16) - 128, L[A] = (O[h + 768 >> 0] + O[j + 1024 >> 0] + O[q + 1280 >> 0] >> 16) - 128, M[A] = (O[h + 1280 >> 0] + O[j + 1536 >> 0] + O[q + 1792 >> 0] >> 16) - 128;
                        c = p(K, B, c, t, v), d = p(L, C, d, u, w), e = p(M, C, e, u, w), f += 32
                    }
                    N += 8
                }
                if (J >= 0) {
                    var P = [];
                    P[1] = J + 1, P[0] = (1 << J + 1) - 1, g(P)
                }
                i(65497);
                var Q = "data:image/jpeg;base64," + btoa(H.join(""));
                return H = [], Q
            }, s()
        }

        return a.encode = function (b, c) {
            var d = new a(c);
            return d.encode(b)
        }, a
    }), b("runtime/html5/androidpatch", ["runtime/html5/util", "runtime/html5/jpegencoder", "base"], function (a, b, c) {
        var d, e = a.canvasToDataUrl;
        a.canvasToDataUrl = function (a, f, g) {
            var h, i, j, k, l;
            return c.os.android ? ("image/jpeg" === f && "undefined" == typeof d && (k = e.apply(null, arguments), l = k.split(","), k = ~l[0].indexOf("base64") ? atob(l[1]) : decodeURIComponent(l[1]), k = k.substring(0, 2), d = 255 === k.charCodeAt(0) && 216 === k.charCodeAt(1)), "image/jpeg" !== f || d ? e.apply(null, arguments) : (i = a.width, j = a.height, h = a.getContext("2d"), b.encode(h.getImageData(0, 0, i, j), g))) : e.apply(null, arguments)
        }
    }), b("runtime/html5/image", ["base", "runtime/html5/runtime", "runtime/html5/util"], function (a, b, c) {
        var d = "data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs%3D";
        return b.register("Image", {
            modified: !1, init: function () {
                var a = this, b = new Image;
                b.onload = function () {
                    a._info = {
                        type: a.type,
                        width: this.width,
                        height: this.height
                    }, a._metas || "image/jpeg" !== a.type ? a.owner.trigger("load") : c.parseMeta(a._blob, function (b, c) {
                        a._metas = c, a.owner.trigger("load")
                    })
                }, b.onerror = function () {
                    a.owner.trigger("error")
                }, a._img = b
            }, loadFromBlob: function (a) {
                var b = this, d = b._img;
                b._blob = a, b.type = a.type, d.src = c.createObjectURL(a.getSource()), b.owner.once("load", function () {
                    c.revokeObjectURL(d.src)
                })
            }, resize: function (a, b) {
                var c = this._canvas || (this._canvas = document.createElement("canvas"));
                this._resize(this._img, c, a, b), this._blob = null, this.modified = !0, this.owner.trigger("complete", "resize")
            }, crop: function (a, b, c, d, e) {
                var f = this._canvas || (this._canvas = document.createElement("canvas")), g = this.options,
                    h = this._img, i = h.naturalWidth, j = h.naturalHeight, k = this.getOrientation();
                e = e || 1, f.width = c, f.height = d, g.preserveHeaders || this._rotate2Orientaion(f, k), this._renderImageToCanvas(f, h, -a, -b, i * e, j * e), this._blob = null, this.modified = !0, this.owner.trigger("complete", "crop")
            }, getAsBlob: function (a) {
                var b, d = this._blob, e = this.options;
                if (a = a || this.type, this.modified || this.type !== a) {
                    if (b = this._canvas, "image/jpeg" === a) {
                        if (d = c.canvasToDataUrl(b, a, e.quality), e.preserveHeaders && this._metas && this._metas.imageHead) return d = c.dataURL2ArrayBuffer(d), d = c.updateImageHead(d, this._metas.imageHead), d = c.arrayBufferToBlob(d, a)
                    } else d = c.canvasToDataUrl(b, a);
                    d = c.dataURL2Blob(d)
                }
                return d
            }, getAsDataUrl: function (a) {
                var b = this.options;
                return a = a || this.type, "image/jpeg" === a ? c.canvasToDataUrl(this._canvas, a, b.quality) : this._canvas.toDataURL(a)
            }, getOrientation: function () {
                return this._metas && this._metas.exif && this._metas.exif.get("Orientation") || 1
            }, info: function (a) {
                return a ? (this._info = a, this) : this._info
            }, meta: function (a) {
                return a ? (this._metas = a, this) : this._metas
            }, destroy: function () {
                var a = this._canvas;
                this._img.onload = null, a && (a.getContext("2d").clearRect(0, 0, a.width, a.height), a.width = a.height = 0, this._canvas = null), this._img.src = d, this._img = this._blob = null
            }, _resize: function (a, b, c, d) {
                var e, f, g, h, i, j = this.options, k = a.width, l = a.height, m = this.getOrientation();
                ~[5, 6, 7, 8].indexOf(m) && (c ^= d, d ^= c, c ^= d), e = Math[j.crop ? "max" : "min"](c / k, d / l), j.allowMagnify || (e = Math.min(1, e)), f = k * e, g = l * e, j.crop ? (b.width = c, b.height = d) : (b.width = f, b.height = g), h = (b.width - f) / 2, i = (b.height - g) / 2, j.preserveHeaders || this._rotate2Orientaion(b, m), this._renderImageToCanvas(b, a, h, i, f, g)
            }, _rotate2Orientaion: function (a, b) {
                var c = a.width, d = a.height, e = a.getContext("2d");
                switch (b) {
                    case 5:
                    case 6:
                    case 7:
                    case 8:
                        a.width = d, a.height = c
                }
                switch (b) {
                    case 2:
                        e.translate(c, 0), e.scale(-1, 1);
                        break;
                    case 3:
                        e.translate(c, d), e.rotate(Math.PI);
                        break;
                    case 4:
                        e.translate(0, d), e.scale(1, -1);
                        break;
                    case 5:
                        e.rotate(.5 * Math.PI), e.scale(1, -1);
                        break;
                    case 6:
                        e.rotate(.5 * Math.PI), e.translate(0, -d);
                        break;
                    case 7:
                        e.rotate(.5 * Math.PI), e.translate(c, -d), e.scale(-1, 1);
                        break;
                    case 8:
                        e.rotate(-.5 * Math.PI), e.translate(-c, 0)
                }
            }, _renderImageToCanvas: function () {
                function b(a, b, c) {
                    var d, e, f, g = document.createElement("canvas"), h = g.getContext("2d"), i = 0, j = c, k = c;
                    for (g.width = 1, g.height = c, h.drawImage(a, 0, 0), d = h.getImageData(0, 0, 1, c).data; k > i;) e = d[4 * (k - 1) + 3], 0 === e ? j = k : i = k, k = j + i >> 1;
                    return f = k / c, 0 === f ? 1 : f
                }

                function c(a) {
                    var b, c, d = a.naturalWidth, e = a.naturalHeight;
                    return d * e > 1048576 ? (b = document.createElement("canvas"), b.width = b.height = 1, c = b.getContext("2d"), c.drawImage(a, -d + 1, 0), 0 === c.getImageData(0, 0, 1, 1).data[3]) : !1
                }

                return a.os.ios ? a.os.ios >= 7 ? function (a, c, d, e, f, g) {
                    var h = c.naturalWidth, i = c.naturalHeight, j = b(c, h, i);
                    return a.getContext("2d").drawImage(c, 0, 0, h * j, i * j, d, e, f, g)
                } : function (a, d, e, f, g, h) {
                    var i, j, k, l, m, n, o, p = d.naturalWidth, q = d.naturalHeight, r = a.getContext("2d"), s = c(d),
                        t = "image/jpeg" === this.type, u = 1024, v = 0, w = 0;
                    for (s && (p /= 2, q /= 2), r.save(), i = document.createElement("canvas"), i.width = i.height = u, j = i.getContext("2d"), k = t ? b(d, p, q) : 1, l = Math.ceil(u * g / p), m = Math.ceil(u * h / q / k); q > v;) {
                        for (n = 0, o = 0; p > n;) j.clearRect(0, 0, u, u), j.drawImage(d, -n, -v), r.drawImage(i, 0, 0, u, u, e + o, f + w, l, m), n += u, o += l;
                        v += u, w += m
                    }
                    r.restore(), i = j = null
                } : function (b) {
                    var c = a.slice(arguments, 1), d = b.getContext("2d");
                    d.drawImage.apply(d, c)
                }
            }()
        })
    }), b("runtime/html5/transport", ["base", "runtime/html5/runtime"], function (a, b) {
        var c = a.noop, d = a.$;
        return b.register("Transport", {
            init: function () {
                this._status = 0, this._response = null
            }, send: function () {
                var b, c, e, f = this.owner, g = this.options, h = this._initAjax(), i = f._blob, j = g.server;
                g.sendAsBinary ? (j += g.attachInfoToQuery !== !1 ? (/\?/.test(j) ? "&" : "?") + d.param(f._formData) : "", c = i.getSource()) : (b = new FormData, d.each(f._formData, function (a, c) {
                    b.append(a, c)
                }), b.append(g.fileVal, i.getSource(), g.filename || f._formData.name || "")), g.withCredentials && "withCredentials" in h ? (h.open(g.method, j, !0), h.withCredentials = !0) : h.open(g.method, j), this._setRequestHeader(h, g.headers), c ? (h.overrideMimeType && h.overrideMimeType("application/octet-stream"), a.os.android ? (e = new FileReader, e.onload = function () {
                    h.send(this.result), e = e.onload = null
                }, e.readAsArrayBuffer(c)) : h.send(c)) : h.send(b)
            }, getResponse: function () {
                return this._response
            }, getResponseAsJson: function () {
                return this._parseJson(this._response)
            }, getResponseHeaders: function () {
                return this._headers
            }, getStatus: function () {
                return this._status
            }, abort: function () {
                var a = this._xhr;
                a && (a.upload.onprogress = c, a.onreadystatechange = c, a.abort(), this._xhr = a = null)
            }, destroy: function () {
                this.abort()
            }, _parseHeader: function (a) {
                var b = {};
                return a && a.replace(/^([^\:]+):(.*)$/gm, function (a, c, d) {
                    b[c.trim()] = d.trim()
                }), b
            }, _initAjax: function () {
                var a = this, b = new XMLHttpRequest, d = this.options;
                return !d.withCredentials || "withCredentials" in b || "undefined" == typeof XDomainRequest || (b = new XDomainRequest), b.upload.onprogress = function (b) {
                    var c = 0;
                    return b.lengthComputable && (c = b.loaded / b.total), a.trigger("progress", c)
                }, b.onreadystatechange = function () {
                    if (4 === b.readyState) {
                        b.upload.onprogress = c, b.onreadystatechange = c, a._xhr = null, a._status = b.status;
                        var d = "|", e = d + b.status + d + b.statusText;
                        return b.status >= 200 && b.status < 300 ? (a._response = b.responseText, a._headers = a._parseHeader(b.getAllResponseHeaders()), a.trigger("load")) : b.status >= 500 && b.status < 600 ? (a._response = b.responseText, a._headers = a._parseHeader(b.getAllResponseHeaders()), a.trigger("error", "server" + e)) : a.trigger("error", a._status ? "http" + e : "abort")
                    }
                }, a._xhr = b, b
            }, _setRequestHeader: function (a, b) {
                d.each(b, function (b, c) {
                    a.setRequestHeader(b, c)
                })
            }, _parseJson: function (a) {
                var b;
                try {
                    b = JSON.parse(a)
                } catch (c) {
                    b = {}
                }
                return b
            }
        })
    }), b("runtime/html5/md5", ["runtime/html5/runtime"], function (a) {
        var b = function (a, b) {
            return 4294967295 & a + b
        }, c = function (a, c, d, e, f, g) {
            return c = b(b(c, a), b(e, g)), b(c << f | c >>> 32 - f, d)
        }, d = function (a, b, d, e, f, g, h) {
            return c(b & d | ~b & e, a, b, f, g, h)
        }, e = function (a, b, d, e, f, g, h) {
            return c(b & e | d & ~e, a, b, f, g, h)
        }, f = function (a, b, d, e, f, g, h) {
            return c(b ^ d ^ e, a, b, f, g, h)
        }, g = function (a, b, d, e, f, g, h) {
            return c(d ^ (b | ~e), a, b, f, g, h)
        }, h = function (a, c) {
            var h = a[0], i = a[1], j = a[2], k = a[3];
            h = d(h, i, j, k, c[0], 7, -680876936), k = d(k, h, i, j, c[1], 12, -389564586), j = d(j, k, h, i, c[2], 17, 606105819), i = d(i, j, k, h, c[3], 22, -1044525330), h = d(h, i, j, k, c[4], 7, -176418897), k = d(k, h, i, j, c[5], 12, 1200080426), j = d(j, k, h, i, c[6], 17, -1473231341), i = d(i, j, k, h, c[7], 22, -45705983), h = d(h, i, j, k, c[8], 7, 1770035416), k = d(k, h, i, j, c[9], 12, -1958414417), j = d(j, k, h, i, c[10], 17, -42063), i = d(i, j, k, h, c[11], 22, -1990404162), h = d(h, i, j, k, c[12], 7, 1804603682), k = d(k, h, i, j, c[13], 12, -40341101), j = d(j, k, h, i, c[14], 17, -1502002290), i = d(i, j, k, h, c[15], 22, 1236535329), h = e(h, i, j, k, c[1], 5, -165796510), k = e(k, h, i, j, c[6], 9, -1069501632), j = e(j, k, h, i, c[11], 14, 643717713), i = e(i, j, k, h, c[0], 20, -373897302), h = e(h, i, j, k, c[5], 5, -701558691), k = e(k, h, i, j, c[10], 9, 38016083), j = e(j, k, h, i, c[15], 14, -660478335), i = e(i, j, k, h, c[4], 20, -405537848), h = e(h, i, j, k, c[9], 5, 568446438), k = e(k, h, i, j, c[14], 9, -1019803690), j = e(j, k, h, i, c[3], 14, -187363961), i = e(i, j, k, h, c[8], 20, 1163531501), h = e(h, i, j, k, c[13], 5, -1444681467), k = e(k, h, i, j, c[2], 9, -51403784), j = e(j, k, h, i, c[7], 14, 1735328473), i = e(i, j, k, h, c[12], 20, -1926607734), h = f(h, i, j, k, c[5], 4, -378558), k = f(k, h, i, j, c[8], 11, -2022574463), j = f(j, k, h, i, c[11], 16, 1839030562), i = f(i, j, k, h, c[14], 23, -35309556), h = f(h, i, j, k, c[1], 4, -1530992060), k = f(k, h, i, j, c[4], 11, 1272893353), j = f(j, k, h, i, c[7], 16, -155497632), i = f(i, j, k, h, c[10], 23, -1094730640), h = f(h, i, j, k, c[13], 4, 681279174), k = f(k, h, i, j, c[0], 11, -358537222), j = f(j, k, h, i, c[3], 16, -722521979), i = f(i, j, k, h, c[6], 23, 76029189), h = f(h, i, j, k, c[9], 4, -640364487), k = f(k, h, i, j, c[12], 11, -421815835), j = f(j, k, h, i, c[15], 16, 530742520), i = f(i, j, k, h, c[2], 23, -995338651), h = g(h, i, j, k, c[0], 6, -198630844), k = g(k, h, i, j, c[7], 10, 1126891415), j = g(j, k, h, i, c[14], 15, -1416354905), i = g(i, j, k, h, c[5], 21, -57434055), h = g(h, i, j, k, c[12], 6, 1700485571), k = g(k, h, i, j, c[3], 10, -1894986606), j = g(j, k, h, i, c[10], 15, -1051523), i = g(i, j, k, h, c[1], 21, -2054922799), h = g(h, i, j, k, c[8], 6, 1873313359), k = g(k, h, i, j, c[15], 10, -30611744), j = g(j, k, h, i, c[6], 15, -1560198380), i = g(i, j, k, h, c[13], 21, 1309151649), h = g(h, i, j, k, c[4], 6, -145523070), k = g(k, h, i, j, c[11], 10, -1120210379), j = g(j, k, h, i, c[2], 15, 718787259), i = g(i, j, k, h, c[9], 21, -343485551), a[0] = b(h, a[0]), a[1] = b(i, a[1]), a[2] = b(j, a[2]), a[3] = b(k, a[3])
        }, i = function (a) {
            var b, c = [];
            for (b = 0; 64 > b; b += 4) c[b >> 2] = a.charCodeAt(b) + (a.charCodeAt(b + 1) << 8) + (a.charCodeAt(b + 2) << 16) + (a.charCodeAt(b + 3) << 24);
            return c
        }, j = function (a) {
            var b, c = [];
            for (b = 0; 64 > b; b += 4) c[b >> 2] = a[b] + (a[b + 1] << 8) + (a[b + 2] << 16) + (a[b + 3] << 24);
            return c
        }, k = function (a) {
            var b, c, d, e, f, g, j = a.length, k = [1732584193, -271733879, -1732584194, 271733878];
            for (b = 64; j >= b; b += 64) h(k, i(a.substring(b - 64, b)));
            for (a = a.substring(b - 64), c = a.length, d = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0], b = 0; c > b; b += 1) d[b >> 2] |= a.charCodeAt(b) << (b % 4 << 3);
            if (d[b >> 2] |= 128 << (b % 4 << 3), b > 55) for (h(k, d), b = 0; 16 > b; b += 1) d[b] = 0;
            return e = 8 * j, e = e.toString(16).match(/(.*?)(.{0,8})$/), f = parseInt(e[2], 16), g = parseInt(e[1], 16) || 0, d[14] = f, d[15] = g, h(k, d), k
        }, l = function (a) {
            var b, c, d, e, f, g, i = a.length, k = [1732584193, -271733879, -1732584194, 271733878];
            for (b = 64; i >= b; b += 64) h(k, j(a.subarray(b - 64, b)));
            for (a = i > b - 64 ? a.subarray(b - 64) : new Uint8Array(0), c = a.length, d = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0], b = 0; c > b; b += 1) d[b >> 2] |= a[b] << (b % 4 << 3);
            if (d[b >> 2] |= 128 << (b % 4 << 3), b > 55) for (h(k, d), b = 0; 16 > b; b += 1) d[b] = 0;
            return e = 8 * i, e = e.toString(16).match(/(.*?)(.{0,8})$/), f = parseInt(e[2], 16), g = parseInt(e[1], 16) || 0, d[14] = f, d[15] = g, h(k, d), k
        }, m = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "a", "b", "c", "d", "e", "f"], n = function (a) {
            var b, c = "";
            for (b = 0; 4 > b; b += 1) c += m[15 & a >> 8 * b + 4] + m[15 & a >> 8 * b];
            return c
        }, o = function (a) {
            var b;
            for (b = 0; b < a.length; b += 1) a[b] = n(a[b]);
            return a.join("")
        }, p = function (a) {
            return o(k(a))
        }, q = function () {
            this.reset()
        };
        return "5d41402abc4b2a76b9719d911017c592" !== p("hello") && (b = function (a, b) {
            var c = (65535 & a) + (65535 & b), d = (a >> 16) + (b >> 16) + (c >> 16);
            return d << 16 | 65535 & c
        }), q.prototype.append = function (a) {
            return /[\u0080-\uFFFF]/.test(a) && (a = unescape(encodeURIComponent(a))), this.appendBinary(a), this
        }, q.prototype.appendBinary = function (a) {
            this._buff += a, this._length += a.length;
            var b, c = this._buff.length;
            for (b = 64; c >= b; b += 64) h(this._state, i(this._buff.substring(b - 64, b)));
            return this._buff = this._buff.substr(b - 64), this
        }, q.prototype.end = function (a) {
            var b, c, d = this._buff, e = d.length, f = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            for (b = 0; e > b; b += 1) f[b >> 2] |= d.charCodeAt(b) << (b % 4 << 3);
            return this._finish(f, e), c = a ? this._state : o(this._state), this.reset(), c
        }, q.prototype._finish = function (a, b) {
            var c, d, e, f = b;
            if (a[f >> 2] |= 128 << (f % 4 << 3), f > 55) for (h(this._state, a), f = 0; 16 > f; f += 1) a[f] = 0;
            c = 8 * this._length, c = c.toString(16).match(/(.*?)(.{0,8})$/), d = parseInt(c[2], 16), e = parseInt(c[1], 16) || 0, a[14] = d, a[15] = e, h(this._state, a)
        }, q.prototype.reset = function () {
            return this._buff = "", this._length = 0, this._state = [1732584193, -271733879, -1732584194, 271733878], this
        }, q.prototype.destroy = function () {
            delete this._state, delete this._buff, delete this._length
        }, q.hash = function (a, b) {
            /[\u0080-\uFFFF]/.test(a) && (a = unescape(encodeURIComponent(a)));
            var c = k(a);
            return b ? c : o(c)
        }, q.hashBinary = function (a, b) {
            var c = k(a);
            return b ? c : o(c)
        }, q.ArrayBuffer = function () {
            this.reset()
        }, q.ArrayBuffer.prototype.append = function (a) {
            var b, c = this._concatArrayBuffer(this._buff, a), d = c.length;
            for (this._length += a.byteLength, b = 64; d >= b; b += 64) h(this._state, j(c.subarray(b - 64, b)));
            return this._buff = d > b - 64 ? c.subarray(b - 64) : new Uint8Array(0), this
        }, q.ArrayBuffer.prototype.end = function (a) {
            var b, c, d = this._buff, e = d.length, f = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            for (b = 0; e > b; b += 1) f[b >> 2] |= d[b] << (b % 4 << 3);
            return this._finish(f, e), c = a ? this._state : o(this._state), this.reset(), c
        }, q.ArrayBuffer.prototype._finish = q.prototype._finish, q.ArrayBuffer.prototype.reset = function () {
            return this._buff = new Uint8Array(0), this._length = 0, this._state = [1732584193, -271733879, -1732584194, 271733878], this
        }, q.ArrayBuffer.prototype.destroy = q.prototype.destroy, q.ArrayBuffer.prototype._concatArrayBuffer = function (a, b) {
            var c = a.length, d = new Uint8Array(c + b.byteLength);
            return d.set(a), d.set(new Uint8Array(b), c), d
        }, q.ArrayBuffer.hash = function (a, b) {
            var c = l(new Uint8Array(a));
            return b ? c : o(c)
        }, a.register("Md5", {
            init: function () {
            }, loadFromBlob: function (a) {
                var b, c, d = a.getSource(), e = 2097152, f = Math.ceil(d.size / e), g = 0, h = this.owner,
                    i = new q.ArrayBuffer, j = this, k = d.mozSlice || d.webkitSlice || d.slice;
                c = new FileReader, b = function () {
                    var l, m;
                    l = g * e, m = Math.min(l + e, d.size), c.onload = function (b) {
                        i.append(b.target.result), h.trigger("progress", {total: a.size, loaded: m})
                    }, c.onloadend = function () {
                        c.onloadend = c.onload = null, ++g < f ? setTimeout(b, 1) : setTimeout(function () {
                            h.trigger("load"), j.result = i.end(), b = a = d = i = null, h.trigger("complete")
                        }, 50)
                    }, c.readAsArrayBuffer(k.call(d, l, m))
                }, b()
            }, getResult: function () {
                return this.result
            }
        })
    }), b("preset/all", ["base", "widgets/filednd", "widgets/filepaste", "widgets/filepicker", "widgets/image", "widgets/queue", "widgets/runtime", "widgets/upload", "widgets/validator", "widgets/md5", "runtime/html5/blob", "runtime/html5/dnd", "runtime/html5/filepaste", "runtime/html5/filepicker", "runtime/html5/imagemeta/exif", "runtime/html5/androidpatch", "runtime/html5/image", "runtime/html5/transport", "runtime/html5/md5"], function (a) {
        return a
    }), b("widgets/log", ["base", "uploader", "widgets/widget"], function (a, b) {
        function c(a) {
            var b = e.extend({}, d, a), c = f.replace(/^(.*)\?/, "$1" + e.param(b)), g = new Image;
            g.src = c
        }

        var d, e = a.$, f = " http://static.tieba.baidu.com/tb/pms/img/st.gif??",
            g = (location.hostname || location.host || "protected").toLowerCase(), h = g && /baidu/i.exec(g);
        if (h) return d = {
            dv: 3,
            master: "webuploader",
            online: /test/.exec(g) ? 0 : 1,
            module: "",
            product: g,
            type: 0
        }, b.register({
            name: "log", init: function () {
                var a = this.owner, b = 0, d = 0;
                a.on("error", function (a) {
                    c({type: 2, c_error_code: a})
                }).on("uploadError", function (a, b) {
                    c({type: 2, c_error_code: "UPLOAD_ERROR", c_reason: "" + b})
                }).on("uploadComplete", function (a) {
                    b++, d += a.size
                }).on("uploadFinished", function () {
                    c({c_count: b, c_size: d}), b = d = 0
                }), c({c_usage: 1})
            }
        })
    }), b("webuploader", ["preset/all", "widgets/log"], function (a) {
        return a
    }), c("webuploader")
});
