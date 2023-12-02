var UEditorConfig = require('./../vendor/ueditor/ueditor.config.js');
var UEditor = require('./../vendor/ueditor/ueditor.all.js');
require('./../lib/ueditor/wechatcustomemotion.js');

var EditorUploadConfig = {
    toolbarCallback: function (cmd, editor) {
        switch (cmd) {
            case 'insertimage':
                if (!window.__selectorDialogServer) {
                    alert('Missing Config : window.__selectorDialogServer')
                    return true
                }
                window.__selectorDialog = new window.api.selectorDialog({
                    server: window.__selectorDialogServer + '/image',
                    callback: function (items) {
                        editor.execCommand('insertHtml', items.map(o => '<p><img src="' + o.path + '" /></p>').join("\n"));
                    }
                }).show();
                return true
            case 'attachment':
                if (!window.__selectorDialogServer) {
                    alert('Missing Config : window.__selectorDialogServer')
                    return true
                }
                window.__selectorDialog = new window.api.selectorDialog({
                    server: window.__selectorDialogServer + '/file',
                    callback: function (items) {
                        editor.execCommand('insertFile', items.map(o => {
                            return {
                                url: o.path,
                                title: o.filename,
                            }
                        }))
                    }
                }).show();
                return true
        }
    },
    imageConfig: {
        disableUpload: true,
        disableOnline: true,
        selectCallback: function (editor, cb) {
            window.__selectorDialog = new window.api.selectorDialog({
                server: window.__selectorDialogServer + '/image',
                callback: function (items) {
                    if (items.length) {
                        cb({
                            path: items[0].path,
                            name: items[0].filename,
                        })
                    }
                }
            }).show();
        }
    },
    videoConfig: {
        disableUpload: true,
        selectCallback: function (editor, cb) {
            window.__selectorDialog = new window.api.selectorDialog({
                server: window.__selectorDialogServer + '/video',
                callback: function (items) {
                    if (items.length) {
                        cb({
                            path: items[0].path,
                            name: items[0].filename,
                        })
                    }
                }
            }).show();
        }
    },
    audioConfig: {
        disableUpload: true,
        selectCallback: function (editor, cb) {
            window.__selectorDialog = new window.api.selectorDialog({
                server: window.__selectorDialogServer + '/audio',
                callback: function (items) {
                    if (items.length) {
                        cb({
                            path: items[0].path,
                            name: items[0].filename,
                        })
                    }
                }
            }).show();
        }
    }
}

function getEditorExtraConfig() {
    var config = {
        formulaConfig: {
            imageUrlTemplate: 'https://latex.codecogs.com/svg.image?{}',
        }
    }
    if (window.__editorFormulaConfig && window.__editorFormulaConfig.imageUrlTemplate) {
        config.formulaConfig.imageUrlTemplate = window.__editorFormulaConfig.imageUrlTemplate;
    }
    return config;
}

var Editor = {
    basic: function (id, option, editorOption) {

        var opt = $.extend({
            server: '',
            width: null,
            height: 100,
            ready: function () {
            }
        }, option);

        var editorBasicToolBars = [
            'fullscreen',
            'source',
            'autotypeset',
            'selectall', 'undo', 'redo',
            'removeformat',
            //'formatmatch',
            //'pasteplain',
            // 'template', '|',
            'paragraph',
            //'fontfamily',
            'fontsize', 'forecolor', 'backcolor', '|',
            //'simpleupload',
            'insertimage',
            'uploadimage',
            'insertvideo',
            'insertaudio',
            'attachment',
            'bold', 'italic', 'underline', //'fontborder',
            'strikethrough',
            'superscript', 'subscript', 'blockquote',
            'insertorderedlist', 'insertunorderedlist',
            'rowspacingtop', 'rowspacingbottom', 'lineheight',
            'indent', 'justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', '|',
            'link', 'unlink',
            'insertcode',
            'formula',
            'attachment',
            'imagenone', 'imageleft', 'imageright', 'imagecenter', '|',
            'inserttable', 'deletetable', 'insertparagraphbeforetable', 'insertrow', 'deleterow', 'insertcol', 'deletecol',
            'mergecells', 'mergeright', 'mergedown', 'splittocells', 'splittorows', 'splittocols',
            // 'wechatcustomemotion',
            '|',
            'contentimport'
        ];

        if (window.__editorBasicToolBars) {
            editorBasicToolBars = window.__editorBasicToolBars;
        }

        if (window.__editorBasicToolBarsExtra) {
            editorBasicToolBars = editorBasicToolBars.concat(window.__editorBasicToolBarsExtra);
        }

        var editorOpt = $.extend({
            toolbars: [
                editorBasicToolBars
            ],
            serverUrl: opt.server,
            wordCount: false,
            elementPathEnabled: false,
            initialFrameHeight: opt.height,
            initialFrameWidth: opt.width,
            enableAutoSave: false,
            pasteplain: false,
            autoHeightEnabled: true,
            focus: false,
        }, EditorUploadConfig, editorOption, getEditorExtraConfig());

        var ueditor = UE.getEditor(id, editorOpt);

        ueditor.ready(function () {
            opt.ready();
        });

        return ueditor;

    },
    simple: function (id, option, editorOption) {

        var opt = $.extend({
            server: '',
            width: null,
            height: 100,
            ready: function () {
            }
        }, option);

        var editorSimpleToolBars = [
            'fontsize', 'forecolor',
            //'backcolor', '|',
            'insertimage',
            'uploadimage', 'bold', 'italic', 'underline',
            //'fontborder',
            'strikethrough',
            'insertcode',
            //'superscript', 'subscript',
            // 'emotion','wechatcustomemotion'
        ];
        if (window.__editorSimpleToolBars) {
            editorSimpleToolBars = window.__editorSimpleToolBars;
        }
        if (window.__editorSimpleToolBarsExtra) {
            editorSimpleToolBars = editorSimpleToolBars.concat(window.__editorSimpleToolBarsExtra);
        }

        var editorOpt = $.extend({
            toolbars: [
                editorSimpleToolBars
            ],
            serverUrl: opt.server,
            wordCount: false,
            elementPathEnabled: false,
            initialFrameHeight: opt.height,
            initialFrameWidth: opt.width,
            enableAutoSave: false,
            pasteplain: false,
            retainOnlyLabelPasted: true,
            autoHeightEnabled: true,
            focus: false,
        }, EditorUploadConfig, editorOption, getEditorExtraConfig());

        var ueditor = UE.getEditor(id, editorOpt);

        ueditor.ready(function () {
            opt.ready();
        });

        return ueditor;

    },
    raw: UE
};

if (!('api' in window)) {
    window.api = {}
}
window.api.editor = Editor;
if (!('MS' in window)) {
    window.MS = {}
}
window.MS.editor = Editor;
window.MS.editorUploadConfig = EditorUploadConfig;
