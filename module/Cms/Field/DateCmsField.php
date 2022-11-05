<?php


namespace Module\Cms\Field;


use Illuminate\Support\Facades\View;
use ModStart\Core\Input\InputPackage;
use ModStart\Form\Form;

class DateCmsField extends AbstractCmsField
{
    public function name()
    {
        return 'date';
    }

    public function title()
    {
        return '日期';
    }

    public function convertMysqlType($field)
    {
        return "DATE";
    }


    public function prepareInputOrFail($field, InputPackage $input)
    {
        return $input->getDate($field['name']);
    }

    public function renderForForm(Form $form, $field)
    {
        return $form->date($field['name'], $field['title']);
    }

    public function renderForUserInput($field, $record = null)
    {
        return View::make('module::Cms.View.field.date', [
            'field' => $field,
            'record' => $record,
        ])->render();
    }

}
