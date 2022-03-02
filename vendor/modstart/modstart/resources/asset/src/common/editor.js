var UEditorConfig = require('./../vendor/ueditor/ueditor.config.js');
var UEditor = require('./../vendor/ueditor/ueditor.js');
var Emotion = require('./../lib/emotion.js');

UE.commands['uploadimage'] = {
    execCommand: function (cmdName, align) {
        if (!window.__selectorDialogServer) {
            alert('Missing Config : window.__selectorDialogServer')
            return true
        }
        var _this = this;
        window.__selectorDialog = new window.api.selectorDialog({
            server: window.__selectorDialogServer + '/image',
            callback: function (items) {
                _this.execCommand('insertHtml', items.map(o => '<p><img src="' + o.path + '" /></p>').join("\n"));
            }
        }).show();
        return true
    }
};

var editorEmotionDialog = null;
UE.commands['wechatcustomemotion'] = {
    execCommand: function (cmdName, align) {
        var _this = this;
        if (editorEmotionDialog) {
            return false;
        }
        var editorEmotionId = 'editor-wechatcustomemotion';
        var $emotionContainer = $('<div class="editor-wechatcustomemotion"></div>');
        $emotionContainer.attr('id', editorEmotionId);
        for (var i = 0; i < Emotion.records.length; i++) {
            $emotionContainer.append('<a href="javascript:;" class="item" data-key="' + Emotion.records[i].key + '" data-val="' + Emotion.records[i].val + '"><img src="' + window.__msCDN + 'asset/image/emotion/' + Emotion.records[i].val + '@2x.png" /></a>');
        }
        editorEmotionDialog = window.MS.dialog.dialogContent($emotionContainer.prop('outerHTML'), {
            shadeClose: false,
            openCallback: function () {
                $('#' + editorEmotionId).on('click', 'a.item', function () {
                    var key = $(this).attr('data-key');
                    var val = $(this).attr('data-val');
                    var emotionImage = '<img data-key="' + key + '" data-val="' + val + '" src="' + window.__msCDN + 'asset/image/emotion/' + val + '@2x.png" height="20" style="vertical-align:middle;" />';
                    _this.execCommand('insertHtml', emotionImage);
                    window.MS.dialog.dialogClose(editorEmotionDialog);
                    editorEmotionDialog = null;
                });
            }
        });

    }
};

// 公式编辑器
UE.commands['formula'] = {
    execCommand: function () {
        var _this = this;
        var editorFormulaId = 'editor-formula';
        var $formulaEditor = $(`<div class="ub-panel tw-w-80">
<div class="head">
    <div class="title">添加公式</div>
</div>
<div class="body">
    <textarea class="form tw-w-full" rows="3" style="min-height:3rem;" data-formula-content>$x = {-b \\pm \\sqrt{b^2-4ac} \\over 2a}.$</textarea>
</div>
<div class="body">
    <button class="btn" data-formula-confirm>确定</button>
    <span class="ub-text-muted">
        请输入 <a href="https://www.mathjax.org/" target="_blank">mathjax</a> 公式
    </span>
</div>
</div>`);
        $formulaEditor.attr('id', editorFormulaId);
        let editorFormulaDialog = window.MS.dialog.dialogContent($formulaEditor.prop('outerHTML'), {
            shadeClose: false,
            openCallback: function () {
                $('#' + editorFormulaId).on('click', '[data-formula-confirm]', function () {
                    var code = $('#' + editorFormulaId).find('[data-formula-content]').val()
                    if (code) {
                        code = "&nbsp;<code class='formula'>" + code.trim() + "</code>&nbsp;"
                        _this.execCommand('insertHtml', code);
                    } else {
                        window.MS.dialog.tipError('内容为空')
                    }
                    window.MS.dialog.dialogClose(editorFormulaDialog);
                });
            }
        });
    }
};

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
            // 'fullscreen',
            'source',
            'autotypeset',
            //'selectall', 'undo', 'redo',
            'removeformat',
            //'formatmatch',
            //'pasteplain',
            // 'template', '|',
            'paragraph',
            //'fontfamily',
            'fontsize', 'forecolor', //'backcolor', //'|',
            //'simpleupload', 'insertimage',
            'uploadimage',
            'insertvideo',
            //'attachment', 'map',
            'bold', 'italic', 'underline', //'fontborder',
            'strikethrough',
            //'superscript', 'subscript', 'blockquote',
            //'insertorderedlist', 'insertunorderedlist',
            //'rowspacingtop', 'rowspacingbottom', 'lineheight',
            'indent', 'justifyleft', 'justifycenter', 'justifyright', //'justifyjustify', '|',
            'link', 'unlink',
            'insertcode',
            //'imagenone', 'imageleft', 'imageright', 'imagecenter', //'|',
            //'inserttable', 'deletetable', 'insertparagraphbeforetable', 'insertrow', 'deleterow', 'insertcol', 'deletecol',
            //'mergecells', 'mergeright', 'mergedown', 'splittocells', 'splittorows', 'splittocols',
            // 'formula', 'wechatcustomemotion'
        ];

        if ('__editorBasicToolBars' in window) {
            editorBasicToolBars = window.__editorBasicToolBars;
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
            focus: false
        }, editorOption);

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
            'uploadimage', 'bold', 'italic', 'underline',
            //'fontborder',
            'strikethrough',
            'insertcode',
            //'superscript', 'subscript',
            // 'emotion','wechatcustomemotion'
        ];
        if ('__editorSimpleToolBars' in window) {
            editorSimpleToolBars = window.__editorSimpleToolBars;
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
            focus: false
        }, editorOption);

        var ueditor = UE.getEditor(id, editorOpt);

        ueditor.ready(function () {
            opt.ready();
        });

        return ueditor;

    },
    raw: UE
};

window.api.editor = Editor;
if(!('MS' in window)){
    window.MS = {}
}
window.MS.editor = Editor;
