var fs = require('fs');

module.exports = {
    listFiles: function (dir) {
        var filesArr = [];
        dir = /\/$/.test(dir) ? dir : dir + '/';
        (function dir(dirpath, fn) {
            var files = fs.readdirSync(dirpath);
            for (var i = 0; i < files.length; i++) {
                var info = fs.statSync(dirpath + files[i]);
                if (info.isDirectory()) {
                    dir(dirpath + files[i] + '/');
                } else {
                    filesArr.push(dirpath + files[i]);
                }
            }
        })(dir);
        return filesArr;
    }
};






