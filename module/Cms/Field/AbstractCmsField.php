<?php


namespace Module\Cms\Field;


use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use ModStart\Field\AutoRenderedFieldValue;
use ModStart\Form\Form;
use ModStart\Support\Concern\HasFields;

abstract class AbstractCmsField
{
    abstract public function name();

    abstract public function title();

    /**
     * 后台字段保存数据准备
     * @param $data
     * @return mixed
     */
    public function prepareDataOrFail($data)
    {
        return $data;
    }

    /**
     * 用户输入字段获取
     * @param $field
     * @param InputPackage $input
     * @return array|mixed|string|string[]
     */
    public function prepareInputOrFail($field, InputPackage $input)
    {
        return $input->getTrimString($field['name']);
    }

    /**
     * 用户输入字段检查
     * @param $field
     * @param $value
     * @param $data
     * @return array
     */
    public function validateInputValue($field, $value, $data)
    {
        return Response::generateSuccess();
    }

    public function serializeValue($value, $data)
    {
        return $value;
    }

    public function unserializeValue($value, $data)
    {
        return $value;
    }

    public function convertMysqlType($field)
    {
        return "VARCHAR($field[maxLength])";
    }

    public function renderForGrid($viewData)
    {
        return AutoRenderedFieldValue::makeView('modstart::core.field.text-grid', $viewData);
    }

    /**
     * @param Form $form
     * @param $field
     * @return HasFields|null
     */
    public function renderForForm(Form $form, $field)
    {
        return null;
    }

    public function renderForUserInput($field, $record = null)
    {
        return '<div class="ub-text-muted">暂不支持 ' . $field['fieldType'] . '</div>';
    }

    public function renderForFieldEdit()
    {
        return '';
    }

    public function renderForFieldEditScript()
    {
        return 'null';
    }
}
