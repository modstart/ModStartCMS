<?php


namespace Module\Cms\Field;


use Illuminate\Support\Facades\View;
use ModStart\Core\Input\InputPackage;
use ModStart\Form\Form;

class DatetimeCmsField extends AbstractCmsField
{
    public function name()
    {
        return 'datetime';
    }

    public function title()
    {
        return '日期时间';
    }

    public function convertMysqlType($field)
    {
        return "DATETIME";
    }

    public function prepareInputOrFail($field, InputPackage $input)
    {
        return $input->getDatetime($field['name']);
    }

    public function renderForForm(Form $form, $field)
    {
        return $form->datetime($field['name'], $field['title']);
    }

    public function renderForUserInput($field, $record = null)
    {
        return View::make('module::Cms.View.field.datetime', [
            'field' => $field,
            'record' => $record,
        ])->render();
    }
}
