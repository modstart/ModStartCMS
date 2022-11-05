<?php


namespace Module\Cms\Field;


use Illuminate\Support\Facades\View;
use ModStart\Core\Input\InputPackage;
use ModStart\Field\AutoRenderedFieldValue;
use ModStart\Form\Form;

class RichTextCmsField extends AbstractCmsField
{
    public function name()
    {
        return 'richText';
    }

    public function title()
    {
        return '富文本';
    }

    public function convertMysqlType($field)
    {
        return "TEXT";
    }

    public function renderForGrid($viewData)
    {
        return AutoRenderedFieldValue::makeView('modstart::core.field.richHtml-grid', $viewData);
    }

    public function prepareInputOrFail($field, InputPackage $input)
    {
        return $input->getRichContent($field['name']);
    }

    public function renderForForm(Form $form, $field)
    {
        return $form->richHtml($field['name'], $field['title']);
    }

    public function renderForUserInput($field, $record = null)
    {
        return View::make('module::Cms.View.field.richText', [
            'field' => $field,
            'record' => $record,
        ])->render();
    }

}
