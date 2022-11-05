<?php


namespace Module\Cms\Field;


use Illuminate\Support\Facades\View;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\InputPackage;
use ModStart\Field\AutoRenderedFieldValue;
use ModStart\Form\Form;

class CheckboxCmsField extends AbstractCmsField
{
    public function name()
    {
        return 'checkbox';
    }

    public function title()
    {
        return '多选按钮';
    }

    public function prepareDataOrFail($data)
    {
        BizException::throwsIf('选项为空', empty($data['fieldData']['options']));
        $data['fieldData']['options'] = array_filter(array_map(function ($v) {
            return trim($v);
        }, $data['fieldData']['options']));
        BizException::throwsIf('选项为空', empty($data['fieldData']['options']));
        BizException::throwsIf('字段长度错误', $data['maxLength'] < 1 || $data['maxLength'] > 65535);
        $data['fieldData'] = json_encode($data['fieldData']);
        return $data;
    }

    public function prepareInputOrFail($field, InputPackage $input)
    {
        return $input->getArray($field['name']);
    }

    public function serializeValue($value, $data)
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }

    public function unserializeValue($value, $data)
    {
        $value = @json_decode($value, true);
        if (empty($value)) {
            $value = [];
        }
        return $value;
    }


    public function renderForGrid($viewData)
    {
        return AutoRenderedFieldValue::makeView('modstart::core.field.checkbox-grid', $viewData);
    }


    public function renderForForm(Form $form, $field)
    {
        $options = [];
        if (!empty($field['fieldData']['options'])) {
            $options = array_build($field['fieldData']['options'], function ($k, $v) {
                return [$v, $v];
            });
        }
        return $form->checkbox($field['name'], $field['title'])->options($options);
    }

    public function renderForUserInput($field, $record = null)
    {
        return View::make('module::Cms.View.field.checkbox', [
            'field' => $field,
            'record' => $record,
        ])->render();
    }

    public function renderForFieldEdit()
    {
        return View::make('module::Cms.View.field.checkboxFieldEdit', [])->render();
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
