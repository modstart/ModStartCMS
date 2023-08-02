import Editor from '@toast-ui/editor';
import '@toast-ui/editor/dist/i18n/zh-cn';
import '@toast-ui/editor/dist/toastui-editor.css';
import imageCompression from 'browser-image-compression';

var Markdown = {
    basic: function (id, option, editorOption) {
        const opt = $.extend({
            server: null,
            width: null,
            height: '500px',
            initValue: '',
            pasteImage: {
                compress: true,
                maxWidthOrHeight: 2000,
                maxSizeMB: 10,
            },
            ready: function () {
            }
        }, option);

        let editorHtml = '<div id="' + id + 'MarkdownEditor" class="tw-bg-white"></div>';
        let $container = $('#' + id);
        if ($container.is('textarea')) {
            opt.initValue = $container.val();
            $container.replaceWith(editorHtml + '<input type="hidden" id="' + id + '" name="' + $container.attr('name') + '" value="" />');
            $container = $('#' + id);
            $container.val(opt.initValue);
        } else {
            alert('暂不支持的Markdown容器');
            return;
        }


        function createLastButton() {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'toastui-editor-toolbar-icons last';
            button.style.backgroundImage = 'none';
            button.style.margin = '0';
            button.innerHTML = `<i class="fa fa-image"></i>`;
            button.addEventListener('click', function () {
                if (!window.__selectorDialogServer) {
                    alert('Missing Config : window.__selectorDialogServer')
                    return true
                }
                var _this = this;
                window.__selectorDialog = new window.api.selectorDialog({
                    server: window.__selectorDialogServer + '/image',
                    callback: function (items) {
                        var html = [];
                        for (var i = 0; i < items.length; i++) {
                            html.push('![' + items[i].filename + '](' + items[i].path + ')');
                        }
                        editor.insertText(html.join("\n\n"));
                    }
                }).show();
                return false
            });
            return button;
        }

        const editor = new Editor({
            el: document.querySelector('#' + id + 'MarkdownEditor'),
            previewStyle: 'vertical',
            height: opt.height,
            initialValue: opt.initValue,
            hideModeSwitch: true,
            language: 'zh-CN',
            toolbarItems: [
                [
                    {
                        el: createLastButton(),
                        command: 'insertimage',
                        tooltip: '插入图片'
                    },
                    'heading', 'bold', 'italic', 'strike'],
                ['hr', 'quote'],
                ['ul', 'ol', 'task', 'indent', 'outdent'],
                ['table', 'link'],
                ['code', 'codeblock'],
            ],
        });
        editor.on('change', function () {
            var value = editor.getMarkdown();
            $('#' + id).val(value);
        });
        editor.off('addImageBlobHook');
        editor.on('addImageBlobHook', function (file) {
            MS.dialog.loadingOn('正在上传')
            const upload = function (f) {
                MS.file.blobToBase64(f, function (base64) {
                    MS.api.post(window.__selectorDialogServer + '/image', {
                        action: 'uploadAndSaveBase64',
                        filename: file.name,
                        data: base64,
                    }, function (res) {
                        MS.dialog.loadingOff()
                        editor.insertText('![' + file.name + '](' + res.data.fullPath + ')');
                    });
                });
            };
            if (opt.pasteImage.compress) {
                imageCompression(file, {
                    maxSizeMB: opt.pasteImage.maxSizeMB,
                    maxWidthOrHeight: opt.pasteImage.maxWidthOrHeight,
                    useWebWorker: true
                })
                    .then(function (compressedFile) {
                        // console.log('paste image compressed', compressedFile.size / file.size + '%')
                        upload(compressedFile)
                    })
                    .catch(function (error) {
                        MS.dialog.loadingOff()
                        MS.dialog.tipError('图片压缩失败')
                    });
            } else {
                upload(file)
            }
        });
        return editor;
    },
    simple: function (id, option, editorOption) {
        return Markdown.basic(id, option, editorOption);
    }
};

// var SimpleMDE = require('./../vendor/simplemde/simplemde.js');
// var SimpleMDECSS = require('./../vendor/simplemde/simplemde.css');
//
// var Markdown = {
//     basic: function (id, option, editorOption) {
//
//         var opt = $.extend({
//             server: null,
//             width: null,
//             height: 100,
//             ready: function () {
//             }
//         }, option);
//
//         var mdEditor;
//         var toolbar = 'bold,italic,strikethrough,heading,code,quote,unordered-list,ordered-list,clean-block,link'.split(',');
//         toolbar.push({
//             name: "image",
//             action: function () {
//                 if (!window.__selectorDialogServer) {
//                     alert('Missing Config : window.__selectorDialogServer')
//                     return true
//                 }
//                 var _this = this;
//                 window.__selectorDialog = new window.api.selectorDialog({
//                     server: window.__selectorDialogServer + '/image',
//                     callback: function (items) {
//                         // console.log('items', items)
//                         var html = [];
//                         for (var i = 0; i < items.length; i++) {
//                             html.push('![' + items[i].filename + '](' + items[i].path + ')');
//                         }
//                         mdEditor.codemirror.replaceSelection(html.join(" "));
//                     }
//                 }).show();
//                 return true
//             },
//             className: "fa fa-image",
//             title: "图片",
//         });
//         toolbar.push('preview');
//         var editorOpt = $.extend({
//             element: document.getElementById(id),
//             spellChecker: false,
//             toolbar: toolbar,
//             status: false,
//             autoDownloadFontAwesome: false,
//         }, editorOption);
//
//         mdEditor = new SimpleMDE(editorOpt);
//         mdEditor.codemirror.on("change", function () {
//             $('#' + id).val(mdEditor.value());
//         });
//         return mdEditor;
//
//     },
//     simple: function (id, option, editorOption) {
//         return Markdown.basic(id, option, editorOption);
//     }
// };

if (!('api' in window)) {
    window.api = {}
}
window.api.editorMarkdown = Markdown;
if (!('MS' in window)) {
    window.MS = {}
}
window.MS.editorMarkdown = Markdown;
