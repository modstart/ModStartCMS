<?php


namespace Module\Cms\Field;


use Illuminate\Support\Facades\View;
use ModStart\Core\Assets\AssetsUtil;
use ModStart\Form\Form;

class VideoCmsField extends AbstractCmsField
{
    public function name()
    {
        return 'video';
    }

    public function title()
    {
        return '视频';
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

    public function renderForForm(Form $form, $field)
    {
        return $form->video($field['name'], $field['title']);
    }

    public function renderForUserInput($field, $record = null)
    {
        return View::make('module::Cms.View.field.video', [
            'field' => $field,
            'record' => $record,
        ])->render();
    }

}
