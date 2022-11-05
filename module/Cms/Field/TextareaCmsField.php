<?php


namespace Module\Cms\Field;


use Illuminate\Support\Facades\View;
use ModStart\Core\Exception\BizException;
use ModStart\Field\AutoRenderedFieldValue;
use ModStart\Form\Form;

class TextareaCmsField extends AbstractCmsField
{
    public function name()
    {
        return 'textarea';
    }

    public function title()
    {
        return '多行文本';
    }


    public function prepareDataOrFail($data)
    {
        BizException::throwsIf('字段长度错误', $data['maxLength'] < 1 || $data['maxLength'] > 65535);
        return $data;
    }

    public function renderForGrid($viewData)
    {
        return AutoRenderedFieldValue::makeView('modstart::core.field.text-grid', $viewData);
    }

    public function renderForForm(Form $form, $field)
    {
        return $form->textarea($field['name'], $field['title']);
    }


    public function renderForUserInput($field, $record = null)
    {
        return View::make('module::Cms.View.field.textarea', [
            'field' => $field,
            'record' => $record,
        ])->render();
    }

    public function renderForFieldEdit()
    {
        return View::make('module::Cms.View.field.textareaFieldEdit', [])->render();
    }

}
