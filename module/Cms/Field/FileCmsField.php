<?php


namespace Module\Cms\Field;


use Illuminate\Support\Facades\View;
use ModStart\Core\Assets\AssetsUtil;
use ModStart\Core\Input\InputPackage;
use ModStart\Form\Form;

class FileCmsField extends AbstractCmsField
{
    public function name()
    {
        return 'file';
    }

    public function title()
    {
        return '文件';
    }

    public function unserializeValue($value, $data)
    {
        if (!empty($value)) {
            $value = AssetsUtil::fixFull($value);
        }
        return $value;
    }

    public function prepareDataOrFail($data)
    {
        $data['maxLength'] = 200;
        return $data;
    }

    public function prepareInputOrFail($field, InputPackage $input)
    {
        return $input->getFilePath($field['name']);
    }


    public function renderForForm(Form $form, $field)
    {
        return $form->file($field['name'], $field['title']);
    }

    public function renderForUserInput($field, $record = null)
    {
        return View::make('module::Cms.View.field.file', [
            'field' => $field,
            'record' => $record,
        ])->render();
    }
}
