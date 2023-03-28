const FileSaver = require('file-saver');

export const FileUtil = {
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
    download(filename, content, type) {
        // type = type || "text/plain;charset=utf-8"
        type = type || 'application/octet-stream'
        let blob
        if ('object' === typeof content) {
            blob = content
        } else {
            blob = new Blob([content], {type});
        }
        FileSaver.saveAs(blob, filename);
    },
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
    downloadHtml(filename, title, html) {
        FileUtil.download(
            filename,
            `<!DOCTYPE html><html><head><meta charset="UTF-8"><title>${title}</title></head><body>${html}</body></html>`,
            'text/html:charset=utf-8'
        )
    },
    previewHtml(title, html) {
        let winname = window.open('', "_blank", '');
        winname.document.open('text/html', 'replace');
        winname.opener = null
        winname.document.write(`<!DOCTYPE html><html><head><meta charset="UTF-8"><title>${title}</title></head><body>${html}</body></html>`);
        winname.document.title = title
        winname.document.close();
    },
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
