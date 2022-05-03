UE.registerUI('formula', function (editor, uiName) {
    return new UE.ui.Button({
        name: uiName,
        title: '插入公式',
        onclick: function () {
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
                            editor.execCommand('insertHtml', code);
                        } else {
                            window.MS.dialog.tipError('内容为空')
                        }
                        window.MS.dialog.dialogClose(editorFormulaDialog);
                    });
                }
            });
        }
    });
});
