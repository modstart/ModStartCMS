<?php


namespace ModStart\Field;


use Illuminate\Support\Facades\View;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Util\RenderUtil;
use ModStart\Core\Util\SerializeUtil;
use ModStart\Core\Util\StrUtil;
use ModStart\Field\Type\DynamicFieldsType;

/**
 * 动态自选
 * Class ComplexFields
 * @package ModStart\Field
 */
class DynamicFields extends AbstractField
{
    protected $width = 300;
    protected $listable = false;

    protected function setup()
    {
        $this->addVariables([
            'enabledFieldTypes' => null,
        ]);
    }

    public function unserializeValue($value, AbstractField $field)
    {
        if (null === $value) {
            return $value;
        }
        $value = @json_decode($value, true);
        if (empty($value)) {
            $value = [];
        }
        foreach ($value as $i => $v) {
            foreach ([
                         'switch1' => false,
                         'switch2' => false,
                         'text1' => '',
                         'text2' => '',
                     ] as $k => $dv) {
                if (!isset($v['data'][$k])) {
                    $value[$i]['data'][$k] = $dv;
                }
            }
        }
        return $value;
    }

    public function enabledFieldTypes($enabledFieldTypes)
    {
        $this->addVariables(['enabledFieldTypes' => $enabledFieldTypes]);
        return $this;
    }

    public function serializeValue($value, $model)
    {
        return SerializeUtil::jsonEncode($value);
    }

    public function prepareInput($value, $model)
    {
        $value = @json_decode($value, true);
        if (empty($value)) {
            $value = [];
        }
        $nameMap = [];
        foreach ($value as $i => $v) {
            $no = $i + 1;
            if (isset($v['name'])) {
                $value[$i]['name'] = trim(StrUtil::filterSpecialChars($v['name']));
            }
            if (isset($v['title'])) {
                $value[$i]['title'] = trim(StrUtil::filterSpecialChars($v['title']));
            }
            $prefix = "{$this->label} 第{$no}个字段";
            BizException::throwsIf("$prefix 标题不能为空", empty($v['title']));
            BizException::throwsIf("$prefix 标识不能为空", empty($v['name']));
            BizException::throwsIf("$prefix 标识格式不正确", !preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $v['name']));
            BizException::throwsIf("$prefix 标识重复", isset($nameMap[$v['name']]));
            $nameMap[$v['name']] = true;
        }
        return $value;
    }

    public static function getEmptyValueObject($fields)
    {
        $value = [];
        foreach ($fields as $f) {
            $v = null;
            switch ($f['type']) {
                case DynamicFieldsType::TYPE_CHECKBOX:
                case DynamicFieldsType::TYPE_FILES:
                    $v = [];
                    break;
            }
            $value[$f['name']] = $v;
        }
        return $value;
    }

    public static function getDefaultValueObject($fields)
    {
        $value = [];
        foreach ($fields as $f) {
            switch ($f['type']) {
                case DynamicFieldsType::TYPE_SELECT:
                case DynamicFieldsType::TYPE_RADIO:
                    $f['defaultValue'] = null;
                    foreach ($f['data']['options'] as $o) {
                        if (!empty($o['active'])) {
                            $f['defaultValue'] = $o['title'];
                            break;
                        }
                    }
                    break;
                case DynamicFieldsType::TYPE_CHECKBOX:
                    $f['defaultValue'] = [];
                    foreach ($f['data']['options'] as $o) {
                        if (!empty($o['active'])) {
                            $f['defaultValue'][] = $o['title'];
                        }
                    }
                    break;
                case DynamicFieldsType::TYPE_FILES:
                    $f['defaultValue'] = [];
                    break;
            }
            $value[$f['name']] = $f['defaultValue'];
        }
        return $value;
    }

    public static function renderAllFormFieldVue($fields, $param = [])
    {
        return View::make('modstart::core.field.dynamicFields.formFieldVue', [
            'fields' => $fields,
            'param' => $param,
        ])->render();
    }

    public static function renderAllFormVue($fields, $param = [])
    {
        BizException::throwsIf('param[name] must required', !isset($param['name']));
        $fieldsData = self::getDefaultValueObject($fields);
        if (!empty($param['values'])) {
            $fieldsData = array_merge($fieldsData, self::fetchValueObject($fields, $param['values']));
        }
        return RenderUtil::view('modstart::core.field.dynamicFields.formVue', [
            'fields' => $fields,
            'fieldsData' => $fieldsData,
            'param' => $param,
        ]);
    }

    public static function fetchValueObject($fields, $values, $param = [])
    {
        $valueObject = [];
        if (!empty($values)) {
            foreach ($values as $value) {
                $valueObject[$value['name']] = $value['value'];
            }
        }
        foreach ($fields as $f) {
            switch ($f['type']) {
                case DynamicFieldsType::TYPE_CHECKBOX:
                case DynamicFieldsType::TYPE_FILES:
                    if (isset($valueObject[$f['name']])) {
                        $valueObject[$f['name']] = @json_decode($valueObject[$f['name']], true);
                    }
                    if (empty($valueObject[$f['name']])) {
                        $valueObject[$f['name']] = [];
                    }
                    break;
            }
        }
        return $valueObject;
    }

    public static function fetchedValueToString($field, $value, $param = [])
    {
        switch ($field['type']) {
            case DynamicFieldsType::TYPE_TEXT:
            case DynamicFieldsType::TYPE_TEXTAREA:
            case DynamicFieldsType::TYPE_NUMBER:
            case DynamicFieldsType::TYPE_SWITCH:
            case DynamicFieldsType::TYPE_RADIO:
            case DynamicFieldsType::TYPE_SELECT:
            case DynamicFieldsType::TYPE_FILE:
                return $value;
            case DynamicFieldsType::TYPE_CHECKBOX:
            case DynamicFieldsType::TYPE_FILES:
                return join(',', $value);
            default:
                BizException::throws($param['tipPrefix'] . "不支持的字段类型: {$field['type']}");
        }
        return null;
    }

    public static function renderAllDetailTableTr($fields, $valueObject, $param = [])
    {
        return View::make('modstart::core.field.dynamicFields.detailTableTr', [
            'fields' => $fields,
            'valueObject' => $valueObject,
            'param' => $param,
        ])->render();
    }

    public static function renderAllDetailTable($fields, $valueObject, $param = [])
    {
        return View::make('modstart::core.field.dynamicFields.detailTable', [
            'fields' => $fields,
            'valueObject' => $valueObject,
            'param' => $param,
        ])->render();
    }

    public static function fetchInputOrFail($fields, InputPackage $input, $param = [])
    {
        if (!isset($param['tipPrefix'])) {
            $param['tipPrefix'] = '';
        }
        $data = [];
        foreach ($fields as $f) {
            switch ($f['type']) {
                case DynamicFieldsType::TYPE_TEXT:
                case DynamicFieldsType::TYPE_TEXTAREA:
                case DynamicFieldsType::TYPE_NUMBER:
                case DynamicFieldsType::TYPE_SWITCH:
                case DynamicFieldsType::TYPE_RADIO:
                case DynamicFieldsType::TYPE_SELECT:
                    $data[$f['name']] = $input->getTrimString($f['name']);
                    break;
                case DynamicFieldsType::TYPE_CHECKBOX:
                    $data[$f['name']] = $input->getTrimStringArray($f['name']);
                    break;
                case DynamicFieldsType::TYPE_FILE:
                    $data[$f['name']] = $input->getDataUploadedPath($f['name']);
                    break;
                case DynamicFieldsType::TYPE_FILES:
                    $data[$f['name']] = $input->getDataUploadedPathArray($f['name']);
                    break;
                default:
                    BizException::throws($param['tipPrefix'] . "不支持的字段类型: {$f['type']}");
            }
            if (!empty($f['isRequired'])) {
                BizException::throwsIfEmpty($param['tipPrefix'] . $f['title'] . '为空', $data[$f['name']]);
            }
        }
        return $data;
    }
}
