<?php


namespace ModStart\Field;


use Illuminate\Support\Facades\View;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\InputPackage;

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

    /**
     * 准备详情数据
     * @param $keyRecord array 携带自定义字段数据的记录
     * @param $valueRecord array 携带自定义字段值的记录
     * @param $prefix string
     * @param $fieldCount int
     * @return array
     */
    public static function buildRecordFieldsValues($keyRecord, $valueRecord, $prefix = 'fieldCustom', $fieldCount = 5)
    {
        self::buildFieldsData($keyRecord, $prefix, $fieldCount);
        $pairs = [];
        foreach ($keyRecord['_' . $prefix] as $f) {
            $value = self::prepareDetail($f, $valueRecord[$f['_name']]);
            $pairs[] = [
                'name' => $f['_name'],
                'value' => $value,
                'field' => $f,
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
     * 对表中的自定义字段进行构建，会自动过滤掉空的自定义字段
     * @param $data array 包含了多个自定义字段的数据
     * @param $fieldPrefix string 多个自定义字段格式应该为 fieldCustom1,fieldCustom2,fieldCustom3
     * @param $fieldCount int 自定义字段数量
     * @example 会对自定义字段进行构建，同时生成自定义字段数组
     * {
     *    ...
     *    "_fieldCustom": [
     *         {
     *             "type": "Text",
     *             "title": "文本字段",
     *             "data": {
     *                 "option": []
     *             },
     *             "_name": "fieldCustom1"
     *         }
     *     ]
     *     ...
     * }
     */
    public static function buildFieldsData(&$data, $fieldPrefix = 'fieldCustom', $fieldCount = 5)
    {
        $fieldModules = [];
        for ($i = 1; $i <= $fieldCount; $i++) {
            $field = $data[$fieldPrefix . $i];
            if (is_string($field)) {
                $field = @json_decode($field, true);
            }
            if (empty($field['type']) || empty($field['title'])) {
                continue;
            } else {
                $field['_name'] = $fieldPrefix . $i;
            }
            $fieldModules[] = $field;
        }
        $data['_' . $fieldPrefix] = $fieldModules;
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
                if (!is_array($value)) {
                    $value = @json_decode($value, true);
                    if (empty($value) || !is_array($value)) {
                        $value = [];
                    }
                }
                return $value;
        }
        return null;
    }

    public static function renderForm($field, $fieldName, $value, $param = [])
    {
        if (empty($field['type']) || empty($field['title'])) {
            return '';
        }
        if (!in_array($field['type'], static::$supportTypes)) {
            return '';
        }
        $value = self::prepareDetail($field, $value);
        return View::make('modstart::core.field.customField.form.' . $field['type'], [
            'fieldName' => $fieldName,
            'field' => $field,
            'value' => $value,
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
