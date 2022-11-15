var SimpleMDE = require('./../vendor/simplemde/simplemde.js');
var SimpleMDECSS = require('./../vendor/simplemde/simplemde.css');

var Markdown = {
    basic: function (id, option, editorOption) {

        var opt = $.extend({
            server: null,
            width: null,
            height: 100,
            ready: function () {
            }
        }, option);

        var mdEditor;
        var toolbar = 'bold,italic,strikethrough,heading,code,quote,unordered-list,ordered-list,clean-block,link'.split(',');
        toolbar.push({
            name: "image",
            action: function () {
                if (!window.__selectorDialogServer) {
                    alert('Missing Config : window.__selectorDialogServer')
                    return true
                }
                var _this = this;
                window.__selectorDialog = new window.api.selectorDialog({
                    server: window.__selectorDialogServer + '/image',
                    callback: function (items) {
                        // console.log('items', items)
                        var html = [];
                        for (var i = 0; i < items.length; i++) {
                            html.push('![' + items[i].filename + '](' + items[i].path + ')');
                        }
                        mdEditor.codemirror.replaceSelection(html.join(" "));
                    }
                }).show();
                return true
            },
            className: "fa fa-image",
            title: "图片",
        });
        toolbar.push('preview');
        var editorOpt = $.extend({
            element: document.getElementById(id),
            spellChecker: false,
            toolbar: toolbar,
            status: false,
            autoDownloadFontAwesome: false,
        }, editorOption);

        mdEditor = new SimpleMDE(editorOpt);
        mdEditor.codemirror.on("change", function () {
            $('#' + id).val(mdEditor.value());
        });
        return mdEditor;

    },
    simple: function (id, option, editorOption) {
        return Markdown.basic(id, option, editorOption);
    }
};

if (!('api' in window)) {
    window.api = {}
}
window.api.editorMarkdown = Markdown;
if (!('MS' in window)) {
    window.MS = {}
}
window.MS.editorMarkdown = Markdown;
