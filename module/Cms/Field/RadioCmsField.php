<?php


namespace Module\Cms\Field;


use Illuminate\Support\Facades\View;
use ModStart\Core\Exception\BizException;
use ModStart\Field\AutoRenderedFieldValue;
use ModStart\Form\Form;

class RadioCmsField extends AbstractCmsField
{
    public function name()
    {
        return 'radio';
    }

    public function title()
    {
        return '单选按钮';
    }

    public function prepareDataOrFail($data)
    {
        BizException::throwsIf('选项为空', empty($data['fieldData']['options']));
        $data['fieldData']['options'] = array_filter(array_map(function ($v) {
            return trim($v);
        }, $data['fieldData']['options']));
        BizException::throwsIf('选项为空', empty($data['fieldData']['options']));
        BizException::throwsIf('字段长度错误', $data['maxLength'] < 1 || $data['maxLength'] > 65535);
        return $data;
    }

    public function renderForGrid($viewData)
    {
        return AutoRenderedFieldValue::makeView('modstart::core.field.radio-grid', $viewData);
    }

    public function renderForForm(Form $form, $field)
    {
        $options = [];
        if (!empty($field['fieldData']['options'])) {
            $options = array_build($field['fieldData']['options'], function ($k, $v) {
                return [$v, $v];
            });
        }
        return $form->radio($field['name'], $field['title'])->options($options);
    }

    public function renderForUserInput($field, $record = null)
    {
        return View::make('module::Cms.View.field.radio', [
            'field' => $field,
            'record' => $record,
        ])->render();
    }


    public function renderForFieldEdit()
    {
        return View::make('module::Cms.View.field.radioFieldEdit', [])->render();
    }

    public function renderForFieldEditScript()
    {
        return <<<JS
{
    onDataChange: function (){
        if (!('options' in this.data.fieldData)) {
            this.\$set(this.data, 'fieldData', {options: []})
        }
    }
}
JS;
    }


}
