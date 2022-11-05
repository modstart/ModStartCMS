<?php


namespace Module\Cms\Field;


use Illuminate\Support\Facades\View;
use ModStart\Core\Assets\AssetsUtil;
use ModStart\Core\Input\InputPackage;
use ModStart\Field\AutoRenderedFieldValue;
use ModStart\Form\Form;

class ImageCmsField extends AbstractCmsField
{
    public function name()
    {
        return 'image';
    }

    public function title()
    {
        return '图片';
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
        return $input->getImagePath($field['name']);
    }


    public function renderForGrid($viewData)
    {
        return AutoRenderedFieldValue::makeView('modstart::core.field.image-grid', $viewData);
    }


    public function renderForForm(Form $form, $field)
    {
        return $form->image($field['name'], $field['title']);
    }

    public function renderForUserInput($field, $record = null)
    {
        return View::make('module::Cms.View.field.image', [
            'field' => $field,
            'record' => $record,
        ])->render();
    }

}
