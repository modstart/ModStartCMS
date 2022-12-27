<?php


namespace ModStart\Field;


use Illuminate\Support\Facades\View;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Util\ArrayUtil;

class CustomField extends AbstractField
{
    public static $supportTypes = [
        'Text',
        'Radio',
        'File',
        'Files',
    ];
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
                'field' => $field,
                'record' => $valueRecord,
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

    /**
     * 对标中的自定义字段进行构建
     * @param $data
     * @param $prefix string
     * @param $fieldCount int
     */
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

    public static function prepareInputOrFail($field, $fieldName, InputPackage $input)
    {
        if (empty($field['type']) || empty($field['title'])) {
            return '';
        }
        if (!in_array($field['type'], static::$supportTypes)) {
            return '';
        }
        switch ($field['type']) {
            case 'Text':
            case 'Radio':
                return $input->getTrimString($fieldName);
            case 'File':
                return $input->getFilePath($fieldName);
            case 'Files':
                $data = $input->getJsonFilesPath($fieldName);
                return json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        BizException::throws('未知的自定义字段类型:' . json_encode($field));
    }

    public static function prepareDetail($field, $value)
    {
        if (empty($field['type']) || empty($field['title'])) {
            return '';
        }
        if (!in_array($field['type'], static::$supportTypes)) {
            return '';
        }
        switch ($field['type']) {
            case 'Text':
            case 'Radio':
            case 'File':
                return $value;
            case 'Files':
                $value = @json_decode($value, true);
                if (empty($value) || !is_array($value)) {
                    $value = [];
                }
                return $value;
        }
        return null;
    }

    public static function renderForm($field, $fieldName, $param = [])
    {
        if (empty($field['type']) || empty($field['title'])) {
            return '';
        }
        if (!in_array($field['type'], static::$supportTypes)) {
            return '';
        }
        return View::make('modstart::core.field.customField.form.' . $field['type'], [
            'fieldName' => $fieldName,
            'field' => $field,
            'param' => $param,
        ])->render();
    }

    public static function renderDetail($field, $value)
    {
        if (empty($field['type']) || empty($field['title'])) {
            return '';
        }
        if (!in_array($field['type'], static::$supportTypes)) {
            return '';
        }
        $value = self::prepareDetail($field, $value);
        return View::make('modstart::core.field.customField.detail.' . $field['type'], [
            'field' => $field,
            'value' => $value,
        ])->render();
    }

}
