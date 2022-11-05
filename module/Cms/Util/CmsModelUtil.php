<?php


namespace Module\Cms\Util;


use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use ModStart\Core\Dao\ModelManageUtil;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Exception\BizException;
use Module\Cms\Field\CmsField;
use Module\Cms\Type\CmsMode;

class CmsModelUtil
{
    public static function clearCache()
    {
        Cache::forget('CmsModelAll');
    }

    public static function listModeMap()
    {
        $map = [];
        foreach (CmsMode::getList() as $k => $v) {
            $map[$k] = [];
        }
        foreach (CmsModelUtil::all() as $item) {
            $map[$item['mode']][] = $item['id'];
        }
        return $map;
    }

    public static function getByName($name)
    {
        foreach (self::all() as $model) {
            if ($model['name'] == $name) {
                return $model;
            }
        }
        BizException::throws('模型不存在[name=' . $name . ']');
    }

    public static function get($modelId)
    {
        foreach (self::all() as $model) {
            if ($model['id'] == $modelId) {
                return $model;
            }
        }
        BizException::throws('模型不存在[id=' . $modelId . ']');
    }

    public static function all()
    {
        return Cache::rememberForever('CmsModelAll', function () {
            try {
                $models = ModelUtil::all('cms_model', ['enable' => true]);
                $fields = ModelUtil::all('cms_model_field', ['enable' => true], ['*'], ['sort', 'asc']);
                ModelUtil::decodeRecordsJson($fields, ['fieldData']);
                foreach ($models as $k => $model) {
                    $models[$k]['_customFields'] = [];
                    foreach ($fields as $field) {
                        if ($field['modelId'] == $model['id']) {
                            $models[$k]['_customFields'][] = $field;
                        }
                    }
                }
                return $models;
            } catch (\Exception $e) {
                return [];
            }
        });
    }

    public static function build($model, $fields = [])
    {
        $model = ModelUtil::insert('cms_model', $model);
        self::create($model);
        foreach ($fields as $field) {
            $field['modelId'] = $model['id'];
            if (!isset($field['sort'])) {
                $field['sort'] = ModelUtil::sortNext('cms_model_field', [
                    'modelId' => $model['id'],
                ]);
            }
            if (!isset($field['maxLength'])) {
                $field['maxLength'] = 100;
            }
            if (!isset($field['isRequired'])) {
                $field['isRequired'] = true;
            }
            if (!isset($field['isSearch'])) {
                $field['isSearch'] = false;
            }
            if (!isset($field['isList'])) {
                $field['isList'] = false;
            }
            if (isset($field['fieldData'])) {
                $field['fieldData'] = json_encode($field['fieldData']);
            }
            ModelUtil::insert('cms_model_field', $field);
            self::addField($model, $field);
        }
        self::clearCache();
    }

    public static function create($model)
    {
        $table = "cms_m_$model[name]";
        ModelManageUtil::migrate($table, function ($table, $schema) {
            $schema->create($table, function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->timestamps();
                $table->text('content')->nullable()->comment('内容');
                $table->index(['created_at']);
            });
        });
    }

    public static function drop($model)
    {
        $table = "cms_m_$model[name]";
        ModelManageUtil::dropTable($table);
    }


    private static function convertField($field)
    {
        $f = CmsField::getByNameOrFail($field['fieldType']);
        return $f->convertMysqlType($field);
    }

    public static function addField($model, $field)
    {
        $table = "cms_m_$model[name]";
        $fieldType = self::convertField($field);
        ModelManageUtil::ddlFieldAdd($table, $field['name'], $fieldType);
    }

    public static function editField($model, $oldField, $newField)
    {
        $table = "cms_m_$model[name]";
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

    public static function deleteField($model, $field)
    {
        $table = "cms_m_$model[name]";
        ModelManageUtil::ddlFieldDrop($table, $field['name']);
    }


    public static function decodeCustomField($model, $record)
    {
        foreach ($model['_customFields'] as $f) {
            $value = isset($record[$f['name']]) ? $record[$f['name']] : null;
            $cmsF = CmsField::getByNameOrFail($f['fieldType']);
            $record[$f['name']] = $cmsF->unserializeValue($value, $record);
        }
        return $record;
    }
}
