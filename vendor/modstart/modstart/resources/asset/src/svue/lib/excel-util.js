var cptable = require('codepage/dist/cpexcel.full');
const isUtf8 = require('isutf8');
import XLSX from 'xlsx';

var ExcelReader = function () {
    var me = this
    this._file = null
    this.file = function (file) {
        this._file = file
        return me
    }
    this.parse = function (cb, config) {
        var isCSV = /.*?\.csv$/i.test(me._file.name);
        var isTxt = /.*?\.txt$/i.test(me._file.name);
        var reader = new FileReader();
        reader.onload = function (e) {
            var data = e.target.result
            var workbook = null;
            if (isCSV || isTxt) {
                data = new Uint8Array(data);
                if (isUtf8(data)) {
                    data = cptable.utils.decode(65001, data);
                } else {
                    data = cptable.utils.decode(936, data);
                }
                if (isTxt) {
                    let lines = []
                    data.split("\n").forEach(o => {
                        lines.push([o.trim()])
                    })
                    cb && cb(lines)
                    return
                }
                workbook = XLSX.read(data, {type: 'string'});
            } else {
                workbook = XLSX.read(data, {type: 'binary'});
            }
            var worksheet = workbook.Sheets[workbook.SheetNames[0]];
            var list = XLSX.utils.sheet_to_json(worksheet, {header: 1});
            list = list.map(line => line.map(o => o.toString()))
            cb && cb(list);
        };
        if (isCSV || isTxt) {
            reader.readAsArrayBuffer(me._file);
        } else {
            reader.readAsBinaryString(me._file);
        }
        return me
    }
    return this
};

var ExcelWriter = function () {
    var me = this;
    this._data = null
    this._filename = null
    this.data = function (data) {
        me._data = data;
        return me
    }
    this.filename = function (filename) {
        me._filename = filename
        return me
    }
    this.download = function (cb) {
        var wb = XLSX.utils.book_new(), ws = XLSX.utils.aoa_to_sheet(me._data);
        XLSX.utils.book_append_sheet(wb, ws, 'Data');
        XLSX.writeFile(wb, me._filename);
        cb && cb();
    };
};

export {
    ExcelReader,
    ExcelWriter
}
