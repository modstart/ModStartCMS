var WebUploader = require('./../lib/webuploader/webuploader.js');

require('./../lib/webuploader/webuploader.css');


const tipError = function (msg) {
    if (MS && MS.dialog) {
        MS.dialog.alertError(msg)
    } else {
        alert(msg)
    }
}

WebUploader.Uploader.register({
    'before-send': 'beforeBlockSend',
    'before-send-file': 'beforeSendFile'
}, {
    beforeBlockSend: function (block) {
        var task = new $.Deferred();
        if (block.chunk + 1 >= this.options.chunkUploaded) {
            setTimeout(task.resolve, 0);
        } else {
            setTimeout(task.reject, 0);
        }
        return $.when(task);
    },
    beforeSendFile: function (file) {
        var task = new $.Deferred();
        var me = this;
        var wait = function () {
            if (!file._widgetImageData) {
                setTimeout(wait, 100);
                return;
            }
            var input = {
                'action': 'init',
                'name': file.name,
                'type': file.type,
                'lastModifiedDate': file.lastModifiedDate.toString(),
                'size': file.size,
                'md5': null
            };
            me.owner
                .md5File(file)
                .then(function (val) {
                    input.md5 = val;
                    file.fileMd5 = val;
                    var continueUpload = function () {
                        $.ajax({
                            type: 'POST',
                            url: me.options.server,
                            headers: me.options.headers,
                            data: JSON.stringify(input),
                            contentType: "application/json",
                            dataType: 'json',
                        }).done(function (res) {
                            if (res.code) {
                                tipError(res.msg);
                                task.reject();
                            } else {
                                file._initData = res.data
                                me.options.chunkUploaded = res.data.chunkUploaded;
                                task.resolve();
                            }
                        }).fail(function (res) {
                            tipError('上传出错');
                            task.reject();
                        });
                    };
                    if (me.options.uploadBeforeCheck) {
                        me.options.uploadBeforeCheck(input, file, function () {
                            continueUpload();
                        }, function (msg) {
                            me.owner.cancelFile(file)
                            task.reject(msg);
                        })
                    } else {
                        continueUpload();
                    }
                })
                .fail(function (error) {
                    tipError('上传出错:' + error)
                    task.reject()
                });
        };
        wait();
        return $.when(task);
    }
});

var UploadButton = function (selector, option) {

    var opt = $.extend({
        text: '选择文件',
        server: '/path/to/server',
        sizeLimit: 2 * 1024 * 1024,
        extensions: 'gif,jpg,png,jpeg',
        chunked: true,
        chunkSize: 5 * 1024 * 1024,
        showFileQueue: true,
        fileNumLimit: 1000,
        uploadBeforeCheck: null,
        compress: {
            enable: false,
            maxWidthOrHeight: 4000,
            maxSize: 10 * 1024 * 1024,
        },
        customUpload: null,
        tipError: function (msg) {
            if (MS && MS.dialog) {
                MS.dialog.tipError(msg)
            } else {
                alert(msg)
            }
        },
        callback: function (file, me) {
            // file.name
            // file.size
            // file.path
        },
        start: function () {

        },
        finish: function () {

        },
        callbackQueued: function (file) {

        },
        callbackDequeued: function (file) {

        },
        callbackQueueSuccess: function (file, res) {

        },
        callbackQueueError: function (file, reason) {

        },
        callbackQueueProgress: function (file, percentage) {

        }
    }, option);

    var uploaders = [];
    $(selector).each(function () {

        var me = this;
        var $me = $(this);

        $me.html('<div style="padding:0;margin:0;"><div class="picker">' + opt.text + '</div><ul class="webuploader-list"></ul></div>');

        var $list = $me.find('.webuploader-list');

        var uploader = WebUploader.create({
            auto: true,
            swf: opt.swf,
            server: opt.server,
            pick: {
                id: $me.find('.picker'),
                multiple: opt.fileNumLimit > 1
            },
            fileSingleSizeLimit: opt.sizeLimit,
            chunked: opt.chunked,
            chunkSize: opt.chunkSize,
            chunkRetry: 5,
            threads: 1,
            accept: {
                extensions: opt.extensions
            },
            fileNumLimit: opt.fileNumLimit,
            formData: {},
            duplicate: false,
            uploadBeforeCheck: opt.uploadBeforeCheck,
            compress: opt.compress,
            customUpload: opt.customUpload,
        });

        uploader.on('fileQueued', function (file) {
            opt.callbackQueued(file);
            if (!opt.showFileQueue) {
                return;
            }
            var html = ' <li id="' + file.id + '">' +
                '<div class="progress-box">' +
                '<div class="progress-bar" style="width:0%"></div>' +
                '</div>' +
                '<div class="progress-info"><span class="status"><i class="iconfont icon-loading"></i></span>' + file.name + '</div>' +
                '</li>';
            var $li = $(html);
            $list.append($li);
        });

        uploader.on('fileDequeued', function (file) {
            opt.callbackDequeued(file);
            $('#' + file.id).fadeOut(function () {
                $('#' + file.id).remove();
            });
        });

        uploader.on('uploadProgress', function (file, percentage) {
            opt.callbackQueueProgress(file, percentage);
            var $li = $('#' + file.id);
            $li.find('.progress-bar').css('width', percentage * 100 + '%');
            if (!$li.find('.status .iconfont').is('.icon-refresh')) {
                $li.find('.status').html('<i class="iconfont icon-refresh tw-animate-spin tw-inline-block"></i>');
            }
        });

        uploader.on('uploadAccept', function (file, response, setErrorReason) {
            if (response.code) {
                setErrorReason(response.msg);
                return false;
            }
            return true;
        });

        uploader.on('startUpload', function () {
            opt.start();
        });

        uploader.on('uploadSuccess', function (file, res) {
            opt.callbackQueueSuccess(file, res);
            this.removeFile(file);
            var f = {
                name: res.data.data.filename,
                size: res.data.data.size,
                path: res.data.path,
                fullPath: res.data.fullPath,
                file: file,
            };
            console.log('xxxx', res, f);
            $('#' + file.id).fadeOut(function () {
                $('#' + file.id).remove();
            });
            opt.callback(f, me);
        });

        uploader.on('uploadBeforeSend', function (file, data) {
            data.md5 = file.file.fileMd5
        });

        uploader.on('uploadError', function (file, typeOrMsg) {
            if (typeOrMsg && typeOrMsg.indexOf('ShouldRetryUpload') >= 0) {
                this.retry(file);
                return;
            }
            opt.callbackQueueError(file, typeOrMsg);
            this.removeFile(file);
            if (typeOrMsg) {
                switch (typeOrMsg) {
                    case 'server':
                        opt.tipError(MS.L('Upload Error : %s', MS.L('Server Error')));
                        break
                    default:
                        opt.tipError(MS.L('Upload Error : %s', typeOrMsg));
                        break
                }
            }
        });

        uploader.on('uploadFinished', function () {
            opt.finish();
        });

        uploader.on('error', function (type) {
            if ('Q_TYPE_DENIED' == type) {
                opt.tipError('文件类型不合法（只能上传' + opt.extensions + '文件）');
            } else if ('Q_EXCEED_SIZE_LIMIT' == type) {
                opt.tipError('文件大小不合法');
            } else if ('F_EXCEED_SIZE' == type) {
                opt.tipError('文件大小不合法');
            }
        });

        uploaders.push(uploader);
    });

    return uploaders;
};

if (!('api' in window)) {
    window.api = {}
}
window.api.uploadButton = UploadButton

if (!('MS' in window)) {
    window.MS = {}
}
window.MS.uploadButton = UploadButton
