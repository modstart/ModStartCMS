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

    public static function buildRecordNameValue($keyRecord, $valueRecord, $prefix = 'fieldCustom', $fieldCount = 5)
    {
        $pairs = [];
        for ($i = 1; $i <= $fieldCount; $i++) {
            if (empty($keyRecord[$prefix . $i])) {
                continue;
            }
            $field = $keyRecord[$prefix . $i];
            if (is_string($field)) {
                $field = @json_decode($field, true);
            }
            if (empty($field['type']) || empty($field['title'])) {
                continue;
            }
            $pairs[] = [
                'name' => $field['title'],
                'value' => $valueRecord[$prefix . $i],
            ];
        }
        return $pairs;
    }

    public static function hasFields($data, $prefix = 'fieldCustom', $fieldCount = 5)
    {
        for ($i = 1; $i <= $fieldCount; $i++) {
            if (empty($data[$prefix . $i])) {
                continue;
            }
            $field = $data[$prefix . $i];
            if (is_string($field)) {
                $field = @json_decode($field, true);
            }
            if (empty($field['type']) || empty($field['title'])) {
                continue;
            }
            return true;
        }
        return false;
    }

    public static function unbuildTableFieldRow(&$data, $prefix = 'fieldCustom', $fieldCount = 5)
    {
        $fieldModules = [];
        for ($i = 1; $i <= $fieldCount; $i++) {
            if (empty($data[$prefix . $i])) {
                continue;
            }
            $field = $data[$prefix . $i];
            if (is_string($field)) {
                $field = @json_decode($field, true);
            }
            if (empty($field['type']) || empty($field['title'])) {
                continue;
            }
            $field['_name'] = $prefix . $i;
            $fieldModules[] = $field;
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
