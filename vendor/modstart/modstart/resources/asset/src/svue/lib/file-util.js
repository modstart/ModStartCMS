const FileSaver = require('file-saver');

export const FileUtil = {
    /**
     * @Util blob转base64
     * @method MS.file.blobToBase64
     * @param blob Blob 对象
     * @param callback Function 回调函数
     */
    blobToBase64(blob, callback) {
        const reader = new FileReader();
        reader.onload = function (e) {
            callback(e.target.result);
        };
        reader.readAsDataURL(blob);
    },
    /**
     * @Util base64转blob
     * @method MS.file.base64toBlob
     * @param b64Data String base64字符串
     * @param contentType String 文件类型
     * @param sliceSize Number 分片大小
     * @return Blob 文件对象
     */
    base64toBlob(b64Data, contentType = '', sliceSize = 512) {
        const byteCharacters = atob(b64Data);
        const byteArrays = [];
        for (let offset = 0; offset < byteCharacters.length; offset += sliceSize) {
            const slice = byteCharacters.slice(offset, offset + sliceSize);
            const byteNumbers = new Array(slice.length);
            for (let i = 0; i < slice.length; i++) {
                byteNumbers[i] = slice.charCodeAt(i);
            }
            const byteArray = new Uint8Array(byteNumbers);
            byteArrays.push(byteArray);
        }
        return new Blob(byteArrays, {type: contentType});
    },
    /**
     * @Util 下载URL为Blob
     * @method MS.file.downloadContent
     * @param url String 下载地址
     * @param option Object 配置项
     */
    downloadContent(url, option) {
        option = Object.assign({
            start: function () {
            },
            end: function () {
            },
            error: function (msg) {

            },
            success: function (data) {

            }
        }, option)
        option.start()
        const xhr = new XMLHttpRequest();
        xhr.open('GET', url);
        xhr.responseType = 'arraybuffer';
        xhr.onload = function () {
            if (xhr.status === 200) {
                const blob = new Blob([xhr.response], {type: 'application/octet-stream'});
                option.success(blob)
            } else {
                option.error('下载文件出现错误')
            }
            option.end()
        };
        xhr.send();
    },
    /**
     * @Util 下载文件
     * @method MS.file.download
     * @param filename String 文件名
     * @param content String|Blob 文件内容
     * @param type String 文件类型
     */
    download(filename, content, type) {
        type = type || 'application/octet-stream'
        let blob
        if (content instanceof Blob) {
            blob = content
        } else {
            blob = new Blob([content], {type});
        }
        FileSaver.saveAs(blob, filename);
    },
    /**
     * @Util 下载CSV
     * @method MS.file.downloadCSV
     * @param filename String 文件名
     * @param data Array<Array> 数据
     */
    downloadCSV(filename, data) {
        let lines = []
        data.forEach(o => {
            let line = []
            o.forEach(oo => {
                line.push(JSON.stringify(oo))
            })
            lines.push(line.join(","))
        })
        FileUtil.download(filename, lines.join("\n"), 'text/csv;charset=utf-8')
    },
    /**
     * @Util 下载为HTML文件
     * @method MS.file.downloadJSON
     * @param filename String 文件名
     * @param title String 标题
     * @param html String 内容
     */
    downloadHtml(filename, title, html) {
        FileUtil.download(
            filename,
            `<!DOCTYPE html><html><head><meta charset="UTF-8"><title>${title}</title></head><body>${html}</body></html>`,
            'text/html:charset=utf-8'
        )
    },
    /**
     * @Util 预览HTML
     * @method MS.file.previewHtml
     * @param title String 标题
     * @param html String 内容
     */
    previewHtml(title, html) {
        let winname = window.open('', "_blank", '');
        winname.document.open('text/html', 'replace');
        winname.opener = null
        winname.document.write(`<!DOCTYPE html><html><head><meta charset="UTF-8"><title>${title}</title></head><body>${html}</body></html>`);
        winname.document.title = title
        winname.document.close();
    },
    /**
     * @Util 格式化文件大小
     * @method MS.file.formatSize
     * @param size Number 文件大小
     * @return String 格式化后的文件大小
     */
    formatSize(size) {
        if (size < 1024) {
            return size + 'B'
        } else if (size < 1024 * 1024) {
            return (size / 1024).toFixed(1) + 'KB'
        } else if (size < 1024 * 1024 * 1024) {
            return (size / (1024 * 1024)).toFixed(1) + 'MB'
        } else {
            return (size / (1024 * 1024 * 1024)).toFixed(1) + 'GB'
        }
    }
}
