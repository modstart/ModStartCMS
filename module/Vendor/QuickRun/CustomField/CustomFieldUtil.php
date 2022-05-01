<?php


namespace Module\Vendor\QuickRun\CustomField;


use Illuminate\Support\Facades\Cache;
use ModStart\Core\Dao\ModelManageUtil;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Exception\BizException;
use ModStart\Detail\Detail;
use ModStart\Form\Form;
use ModStart\Grid\Grid;
use ModStart\Grid\GridFilter;

class CustomFieldUtil
{
    public static function clearCache($tableField)
    {
        Cache::forget('QuickRunCustomField:' . $tableField . ':fields');
    }

    public static function deleteField($table, $field)
    {
        ModelManageUtil::ddlFieldDrop($table, $field['name']);
    }

    public static function addField($table, $field)
    {
        $fieldType = self::convertField($field);
        ModelManageUtil::ddlFieldAdd($table, $field['name'], $fieldType);
    }

    public static function editField($table, $oldField, $newField)
    {
        $oldFieldType = self::convertField($oldField);
        $newFieldType = self::convertField($newField);
        if ($oldFieldType != $newFieldType || $oldField['name'] != $newField['name']) {
            if ($oldField['name'] == $newField['name']) {
                ModelManageUtil::ddlFieldModify($table, $newField['name'], $newFieldType);
            } else {
                ModelManageUtil::ddlFieldChange($table, $oldField['name'], $newField['name'], $newFieldType);
            }
        }
    }

    private static function convertField($field)
    {
        switch ($field['fieldType']) {
            case CustomFieldType::TEXT:
            case CustomFieldType::TEXTAREA:
            case CustomFieldType::RADIO:
            case CustomFieldType::SELECT:
            case CustomFieldType::CHECKBOX:
            case CustomFieldType::IMAGE:
            case CustomFieldType::IMAGES:
            case CustomFieldType::FILE:
                return "VARCHAR($field[maxLength])";
            case CustomFieldType::DATE:
                return "DATE";
            case CustomFieldType::DATETIME:
                return "DATETIME";
            case CustomFieldType::RICH_TEXT:
                return "TEXT";
        }
        return null;
    }

    private static function fields($config)
    {
        return Cache::rememberForever('QuickRunCustomField:' . $config['tableField'] . ':fields', function () use ($config) {
            $fields = ModelUtil::all($config['tableField'], [], [
                'name', 'title', 'fieldType', 'fieldData', 'maxLength', 'enable', 'isRequired', 'isSearch', 'isList', 'placeholder'
            ], ['sort', 'asc']);
            ModelUtil::decodeRecordsJson($fields, 'fieldData');
            return $fields;
        });
    }

    public static function fieldsEnabled($config)
    {
        return array_values(array_filter(self::fields($config), function ($o) {
            return $o['enable'];
        }));
    }

    public static function buildCRUDField($config, $hasField)
    {
        if ($hasField instanceof Grid) {
            self::buildGridField($config, $hasField);
        } else if ($hasField instanceof Form) {
            self::buildFormField($config, $hasField);
        } else if ($hasField instanceof Detail) {
            self::buildDetailField($config, $hasField);
        }
    }

    public static function buildGridField($config, Grid $grid)
    {
        $fields = self::fieldsEnabled($config);
        foreach ($fields as $field) {
            if ($field['isList']) {
                $options = [];
                if (!empty($field['fieldData']['options'])) {
                    $options = array_build($field['fieldData']['options'], function ($k, $v) {
                        return [$v, $v];
                    });
                }
                switch ($field['fieldType']) {
                    case CustomFieldType::TEXT:
                        $f = $grid->text($field['name'], $field['title']);
                        break;
                    case CustomFieldType::TEXTAREA:
                        $f = $grid->textarea($field['name'], $field['title']);
                        break;
                    case CustomFieldType::RADIO:
                        $f = $grid->radio($field['name'], $field['title'])->options($options);
                        break;
                    case CustomFieldType::SELECT:
                        $f = $grid->select($field['name'], $field['title'])->options($options);
                        break;
                    case CustomFieldType::CHECKBOX:
                        $f = $grid->checkbox($field['name'], $field['title'])->options($options);
                        break;
                    case CustomFieldType::IMAGE:
                        $f = $grid->image($field['name'], $field['title']);
                        break;
                    case CustomFieldType::IMAGES:
                        $f = $grid->images($field['name'], $field['title']);
                        break;
                    case CustomFieldType::FILE:
                        $f = $grid->file($field['name'], $field['title']);
                        break;
                    case CustomFieldType::DATE:
                        $f = $grid->date($field['name'], $field['title']);
                        break;
                    case CustomFieldType::DATETIME:
                        $f = $grid->datetime($field['name'], $field['title']);
                        break;
                    case CustomFieldType::RICH_TEXT:
                        $f = $grid->richHtml($field['name'], $field['title']);
                        break;
                    default:
                        BizException::throws('未知字段类型' . json_encode($field, JSON_UNESCAPED_UNICODE));
                }
                $f->placeholder($field['placeholder']);
                if ($field['isRequired']) {
                    $f->required();
                }
            }
        }
    }

    public static function buildGridFilter($config, GridFilter $filter)
    {
        $fields = self::fieldsEnabled($config);
        foreach ($fields as $field) {
            if ($field['isSearch']) {
                $options = [];
                if (!empty($field['fieldData']['options'])) {
                    $options = array_build($field['fieldData']['options'], function ($k, $v) {
                        return [$v, $v];
                    });
                }
                switch ($field['fieldType']) {
                    case CustomFieldType::TEXT:
                    case CustomFieldType::TEXTAREA:
                        $filter->like($field['name'], $field['title']);
                        break;
                    case CustomFieldType::RADIO:
                    case CustomFieldType::SELECT:
                        $filter->eq($field['name'], $field['title'])->select($options);
                        break;
                    case CustomFieldType::DATE:
                        $filter->range($field['name'], $field['title'])->date();
                        break;
                    case CustomFieldType::DATETIME:
                        $filter->range($field['name'], $field['title'])->datetime();
                        break;
                }
            }
        }
    }

    public static function buildFormField($config, Form $form)
    {
        $fields = self::fieldsEnabled($config);
        foreach ($fields as $field) {
            $options = [];
            if (!empty($field['fieldData']['options'])) {
                $options = array_build($field['fieldData']['options'], function ($k, $v) {
                    return [$v, $v];
                });
            }
            switch ($field['fieldType']) {
                case CustomFieldType::TEXT:
                    $f = $form->text($field['name'], $field['title']);
                    break;
                case CustomFieldType::TEXTAREA:
                    $f = $form->textarea($field['name'], $field['title']);
                    break;
                case CustomFieldType::RADIO:
                    $f = $form->radio($field['name'], $field['title'])->options($options);
                    break;
                case CustomFieldType::SELECT:
                    $f = $form->select($field['name'], $field['title'])->options($options);
                    break;
                case CustomFieldType::CHECKBOX:
                    $f = $form->checkbox($field['name'], $field['title'])->options($options);
                    break;
                case CustomFieldType::IMAGE:
                    $f = $form->image($field['name'], $field['title']);
                    break;
                case CustomFieldType::IMAGES:
                    $f = $form->images($field['name'], $field['title']);
                    break;
                case CustomFieldType::FILE:
                    $f = $form->file($field['name'], $field['title']);
                    break;
                case CustomFieldType::DATE:
                    $f = $form->date($field['name'], $field['title']);
                    break;
                case CustomFieldType::DATETIME:
                    $f = $form->datetime($field['name'], $field['title']);
                    break;
                case CustomFieldType::RICH_TEXT:
                    $f = $form->richHtml($field['name'], $field['title']);
                    break;
                default:
                    BizException::throws('未知字段类型' . json_encode($field, JSON_UNESCAPED_UNICODE));
            }
            $f->placeholder($field['placeholder']);
            if ($field['isRequired']) {
                $f->required();
            }
        }
    }

    public static function buildDetailField($config, Detail $detail)
    {
        $fields = self::fieldsEnabled($config);
        foreach ($fields as $field) {
            $options = [];
            if (!empty($field['fieldData']['options'])) {
                $options = array_build($field['fieldData']['options'], function ($k, $v) {
                    return [$v, $v];
                });
            }
            switch ($field['fieldType']) {
                case CustomFieldType::TEXT:
                    $f = $detail->text($field['name'], $field['title']);
                    break;
                case CustomFieldType::TEXTAREA:
                    $f = $detail->textarea($field['name'], $field['title']);
                    break;
                case CustomFieldType::RADIO:
                    $f = $detail->radio($field['name'], $field['title'])->options($options);
                    break;
                case CustomFieldType::SELECT:
                    $f = $detail->select($field['name'], $field['title'])->options($options);
                    break;
                case CustomFieldType::CHECKBOX:
                    $f = $detail->checkbox($field['name'], $field['title'])->options($options);
                    break;
                case CustomFieldType::IMAGE:
                    $f = $detail->image($field['name'], $field['title']);
                    break;
                case CustomFieldType::IMAGES:
                    $f = $detail->images($field['name'], $field['title']);
                    break;
                case CustomFieldType::FILE:
                    $f = $detail->file($field['name'], $field['title']);
                    break;
                case CustomFieldType::DATE:
                    $f = $detail->date($field['name'], $field['title']);
                    break;
                case CustomFieldType::DATETIME:
                    $f = $detail->datetime($field['name'], $field['title']);
                    break;
                case CustomFieldType::RICH_TEXT:
                    $f = $detail->richHtml($field['name'], $field['title']);
                    break;
                default:
                    BizException::throws('未知字段类型' . json_encode($field, JSON_UNESCAPED_UNICODE));
            }
            $f->placeholder($field['placeholder']);
            if ($field['isRequired']) {
                $f->required();
            }
        }
    }

    private static function processRecord($fields, $record)
    {
        if (empty($record)) {
            return $record;
        }
        foreach ($fields as $field) {
            if (!isset($record[$field['name']])) {
                continue;
            }
            switch ($field['fieldType']) {
                case CustomFieldType::CHECKBOX:
                case CustomFieldType::IMAGES:
                    $record[$field['name']] = @json_decode($record[$field['name']], true);
                    if (empty($record[$field['name']])) {
                        $record[$field['name']] = [];
                    }
                    break;
            }
        }
        return $record;
    }

    public static function buildRecord($config, $record)
    {
        $fields = self::fields($config);
        return self::processRecord($fields, $record);
    }

    public static function buildRecords($config, $records)
    {
        $fields = self::fields($config);
        foreach ($records as $k => $record) {
            $records[$k] = self::processRecord($fields, $record);
        }
        return $records;
    }

    public static function renderValue($field, $record)
    {
        if (!isset($record[$field['name']])) {
            return '';
        }
        $value = $record[$field['name']];
        switch ($field['fieldType']) {
            case CustomFieldType::TEXT:
            case CustomFieldType::TEXTAREA:
            case CustomFieldType::RADIO:
            case CustomFieldType::SELECT:
            case CustomFieldType::DATE:
            case CustomFieldType::DATETIME:
                return htmlspecialchars($value);
            case CustomFieldType::CHECKBOX:
                return htmlspecialchars(join(',', $value));
            case CustomFieldType::IMAGE:
                return '<div><img src="' . htmlspecialchars($value) . '" style="max-width:100%;" /></div>';
            case CustomFieldType::IMAGES:
                $html = [];
                foreach ($value as $v) {
                    $html[] = '<div><img src="' . htmlspecialchars($v) . '" style="max-width:100%;" /></div>';
                }
                return join('', $html);
            case CustomFieldType::FILE:
                return "<a href='" . htmlspecialchars($value) . "' target='_blank'>" . htmlspecialchars($value) . "</a>";
            case CustomFieldType::RICH_TEXT:
                return "<div class='ub-html'>" . $value . "</div>";
        }
    }
}
