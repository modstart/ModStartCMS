<?php


namespace ModStart\Field;


use Illuminate\Support\Facades\View;

class CustomField extends AbstractField
{
    protected $value = [
        'type' => '',
        'title' => '',
        'data' => [
            'option' => [],
        ],
    ];
    protected $listable = false;

    protected function setup()
    {
        $this->addVariables([
        ]);
    }

    public function unserializeValue($value, AbstractField $field)
    {
        $value = @json_decode($value, true);
        if (empty($value)) {
            $value = [];
        }
        if (!isset($value['type'])) {
            $value['type'] = '';
        }
        if (!isset($value['title'])) {
            $value['title'] = '';
        }
        if (!isset($value['data'])) {
            $value['data'] = [];
        }
        if (!isset($value['data']['option'])) {
            $value['data']['option'] = [];
        }
        return $value;
    }

    public function serializeValue($value, $model)
    {
        return json_encode($value);
    }

    public function prepareInput($value, $model)
    {
        $value = @json_decode($value, true);
        if (empty($value)) {
            $value = [];
        }
        return $value;
    }

    public static function buildRecordNameValue($keyRedord, $valueRedord, $prefix = 'fieldCustom', $fieldCount = 5)
    {
        $pairs = [];
        for ($i = 1; $i <= $fieldCount; $i++) {
            if (empty($keyRedord[$prefix . $i])) {
                continue;
            }
            $module = @json_decode($keyRedord[$prefix . $i], true);
            if (empty($module)) {
                continue;
            }
            $pairs[] = [
                'name' => $module['title'],
                'value' => $valueRedord[$prefix . $i],
            ];
        }
        return $pairs;
    }

    public static function unbuildTableFieldRow(&$data, $prefix = 'fieldCustom', $fieldCount = 5)
    {
        $fieldModules = [];
        for ($i = 1; $i <= $fieldCount; $i++) {
            if (empty($data[$prefix . $i])) {
                continue;
            }
            $module = @json_decode($data[$prefix . $i], true);
            if (empty($module)) {
                continue;
            }
            $fieldModules[] = $module;
        }
        $data['_' . $prefix] = $fieldModules;
    }

    public static function renderField($field, $fieldName)
    {
        if (empty($field['type']) || empty($field['title'])) {
            return '';
        }
        return View::make('modstart::core.field.customField-render', [
            'fieldName' => $fieldName,
            'field' => $field,
        ])->render();
    }
}
