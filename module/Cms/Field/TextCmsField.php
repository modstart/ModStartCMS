<?php


namespace Module\Cms\Field;


use Illuminate\Support\Facades\View;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\StrUtil;
use ModStart\Field\AutoRenderedFieldValue;
use ModStart\Form\Form;

class TextCmsField extends AbstractCmsField
{
    public function name()
    {
        return 'text';
    }

    public function title()
    {
        return '单行文本';
    }

    public function prepareDataOrFail($data)
    {
        BizException::throwsIf('字段长度错误', $data['maxLength'] < 1 || $data['maxLength'] > 65535);
        return $data;
    }

    public function validateInputValue($field, $value, $data)
    {
        if (!empty($field['maxLength']) && StrUtil::mbLengthGt($value, $field['maxLength'])) {
            return Response::generateError("$field[title] 长度不能超过 $field[maxLength]");
        }
        return Response::generateSuccess();
    }


    public function renderForGrid($viewData)
    {
        return AutoRenderedFieldValue::makeView('modstart::core.field.text-grid', $viewData);
    }

    public function renderForForm(Form $form, $field)
    {
        return $form->text($field['name'], $field['title']);
    }


    public function renderForUserInput($field, $record = null)
    {
        return View::make('module::Cms.View.field.text', [
            'field' => $field,
            'record' => $record,
        ])->render();
    }

    public function renderForFieldEdit()
    {
        return View::make('module::Cms.View.field.textFieldEdit', [])->render();
    }

}
