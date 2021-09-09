import Papa from "papaparse";
// Papa.SCRIPT_PATH = '/static/papaparse.js';

var CSVParser = function () {
    var me = this
    this._file = null
    this.file = function (file) {
        this._file = file
        return me
    }
    this.count = function (cb) {
        var count = 0
        Papa.parse(me._file, {
            // worker: true,
            step: function (results, parser) {
                if (results.data[0].length === 1 && results.data[0][0] === '') {
                    return
                }
                count += results.data.length
            },
            complete: function (results, file) {
                cb && cb(me, count)
            }
        })
        return me
    }
    this.head = function (cb) {
        Papa.parse(me._file, {
            // worker: true,
            step: function (results, parser) {
                if (results.data.length === 1 && results.data[0] === '') {
                    return
                }
                cb && cb(me, results.data)
                parser.abort()
            }
        })
        return me
    }
    this.chunk = function (size, cb, config) {
        var data = []
        var index = 0
        config = config || {}
        if (!('interval' in config)) {
            config.interval = 0
        }
        Papa.parse(me._file, {
            // worker: true,
            step: function (results, parser) {
                if (results.data.length === 1 && results.data[0] === '') {
                    return
                }
                data.push(results.data)
                if (data.length >= size) {
                    cb(me, data, index++)
                    data = []
                    if (config.interval > 0) {
                        parser.pause()
                        setTimeout(() => parser.resume(), config.interval)
                    }
                }
            },
            complete: function (results, file) {
                if (data.length >= 0) {
                    cb(me, data, index++)
                }
            }
        })
        return me
    }
    return this
}


export {
    CSVParser
}
