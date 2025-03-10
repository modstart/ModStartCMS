var $ = require('jquery');
var WebUploader = require('./../webuploader/webuploader.js');
import Cookies from 'js-cookie'

const tipError = function (msg) {
    if (MS && MS.dialog) {
        MS.dialog.alertError(msg)
    } else {
        alert(msg)
    }
}

let apiStore = null

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
            (new WebUploader.Uploader())
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

export const UploadButtonUploader = function (selector, option) {
    var opt = $.extend({
        text: 'Upload',
        swf: '/Uploader.swf',
        server: '/path/to/server',
        sizeLimit: 2 * 1024 * 1024,
        extensions: 'gif,jpg,png,jpeg',
        chunked: true,
        chunkSize: 5 * 1024 * 1024,
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
        ready: function (me) {

        },
        callback: function (file, me) {
            // file.name
            // file.size
            // file.path
        },
        finish: function () {

        }
    }, option);

    return $(selector).each(function () {

        var me = this;
        var $me = $(this);

        $me.html('<div style="display:block;padding:0;margin:0;"><div class="picker">' + opt.text + '</div><ul class="webuploader-list"></ul></div>');

        var $list = $me.find('.webuploader-list');

        var headers = {}
        if (apiStore) {
            headers[apiStore.state.api.tokenKey] = Cookies.get(apiStore.state.api.tokenKey)
        }

        var uploader = WebUploader.create({
            auto: true,
            swf: opt.swf,
            server: opt.server,
            pick: $me.find('.picker'),
            fileSingleSizeLimit: opt.sizeLimit,
            chunked: opt.chunked,
            chunkSize: opt.chunkSize,
            chunkRetry: 5,
            threads: 1,
            accept: {
                extensions: opt.extensions
            },
            formData: {},
            headers: headers,
            duplicate: false,
            uploadBeforeCheck: opt.uploadBeforeCheck,
            compress: opt.compress,
            customUpload: opt.customUpload,
        });

        uploader.on('fileQueued', function (file) {
            var html = ' <li id="' + file.id + '">' +
                '<div class="progress-box">' +
                '<div class="progress-bar" style="width:0%"></div>' +
                '</div>' +
                '<div class="progress-info"><span class="status"><i class="iconfont icon-loading"></i></span> ' + file.name + '</div>' +
                '</li>';
            var $li = $(html);
            $list.append($li);
        });

        uploader.on('fileDequeued', function (file) {
            $('#' + file.id).fadeOut(function () {
                $('#' + file.id).remove();
            });
        });

        uploader.on('fileProcessStart', function (type, file) {
            var $li = $('#' + file.id);
            if (!$li.find('.status .iconfont').is('.icon-clues')) {
                $li.find('.status').html('<i class="iconfont icon-clues tw-cursor-pointer tw-animate-spin tw-inline-block"></i>');
            }
            if ('imageCompress' === type) {
                $li.attr('title', window.lang && lang['CompressingImage'] || 'CompressingImage');
            }
        });

        uploader.on('fileProcessEnd', function (type, file) {
            var $li = $('#' + file.id);
            if ('imageCompress' === type) {
                $li.attr('title', '');
            }
        });

        uploader.on('uploadProgress', function (file, percentage) {
            var $li = $('#' + file.id);
            $li.attr('data-tip-popover', Math.floor(percentage * 100) + '%');
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

        uploader.on('uploadSuccess', function (file, res) {
            this.removeFile(file);
            var f = {
                name: res.data.data.filename,
                size: res.data.data.size,
                path: res.data.path,
                fullPath: res.data.preview,
                file: file,
            };
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

        opt.ready(uploader);

    });

}



