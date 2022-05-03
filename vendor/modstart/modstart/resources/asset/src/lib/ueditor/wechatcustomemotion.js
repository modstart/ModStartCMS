var Emotion = require('./../emotion.js');
var CSS = require('./wechatcustomemotion.less');
UE.registerUI('wechatcustomemotion', function (editor, uiName) {
    var EmotionPath = window.__msCDN + 'asset/image/emotion/';
    var editorEmotionDialog = null;
    return new UE.ui.Button({
        name: uiName,
        title: '插入表情',
        cssRules: 'background-position: -500px 0;',
        onclick: function () {
            if (editorEmotionDialog) {
                return false;
            }
            var editorEmotionId = 'editor-wechatcustomemotion';
            var $emotionContainer = $('<div class="editor-wechatcustomemotion"></div>');
            $emotionContainer.attr('id', editorEmotionId);
            for (var i = 0; i < Emotion.records.length; i++) {
                $emotionContainer.append('<a href="javascript:;" class="item" data-key="' + Emotion.records[i].key + '" data-val="' + Emotion.records[i].val + '"><img src="' + EmotionPath + Emotion.records[i].val + '@2x.png" /></a>');
            }
            editorEmotionDialog = window.MS.dialog.dialogContent($emotionContainer.prop('outerHTML'), {
                shadeClose: false,
                openCallback: function () {
                    $('#' + editorEmotionId).on('click', 'a.item', function () {
                        var key = $(this).attr('data-key');
                        var val = $(this).attr('data-val');
                        var emotionImage = '<img data-key="' + key + '" data-val="' + val + '" src="' + EmotionPath + val + '@2x.png" height="20" style="vertical-align:middle;" />';
                        editor.execCommand('insertHtml', emotionImage);
                        window.MS.dialog.dialogClose(editorEmotionDialog);
                        editorEmotionDialog = null;
                    });
                }
            });
        }
    });
});
