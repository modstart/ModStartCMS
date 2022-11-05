<?php


namespace Module\Cms\Field;


use Illuminate\Support\Facades\View;
use ModStart\Core\Assets\AssetsUtil;
use ModStart\Core\Input\InputPackage;
use ModStart\Field\AutoRenderedFieldValue;
use ModStart\Form\Form;

class ImagesCmsField extends AbstractCmsField
{
    public function name()
    {
        return 'images';
    }

    public function title()
    {
        return '多图';
    }

    public function prepareDataOrFail($data)
    {
        $data['maxLength'] = 1000;
        return $data;
    }

    public function prepareInputOrFail($field, InputPackage $input)
    {
        return $input->getImagesPath($field['name']);
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
        if (!empty($value)) {
            $value = array_map(function ($v) {
                return AssetsUtil::fixFull($v);
            }, $value);
        }
        return $value;
    }


    public function renderForGrid($viewData)
    {
        return AutoRenderedFieldValue::makeView('modstart::core.field.images-grid', $viewData);
    }


    public function renderForForm(Form $form, $field)
    {
        return $form->images($field['name'], $field['title']);
    }

    public function renderForUserInput($field, $record = null)
    {
        return View::make('module::Cms.View.field.images', [
            'field' => $field,
            'record' => $record,
        ])->render();
    }

}
