<?php


namespace Module\Cms\Util;


use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use ModStart\Core\Dao\ModelManageUtil;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Exception\BizException;
use Module\Cms\Type\CmsModelContentStatus;
use Module\Cms\Type\CmsModelFieldType;

class CmsModelUtil
{
    public static function clearCache()
    {
        Cache::forget('CmsModelAll');
    }

    public static function getByName($name)
    {
        foreach (self::all() as $model) {
            if ($model['name'] == $name) {
                return $model;
            }
        }
        BizException::throws('模型不存在');
    }

    public static function get($modelId)
    {
        foreach (self::all() as $model) {
            if ($model['id'] == $modelId) {
                return $model;
            }
        }
        BizException::throws('模型不存在');
    }

    public static function all()
    {
        return Cache::rememberForever('CmsModelAll', function () {
            try {
                $models = ModelUtil::all('cms_model');
                $fields = ModelUtil::all('cms_model_field');
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
        ModelUtil::transactionBegin();
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
        ModelUtil::transactionCommit();
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
        switch ($field['fieldType']) {
            case CmsModelFieldType::TEXT:
            case CmsModelFieldType::TEXTAREA:
            case CmsModelFieldType::RADIO:
            case CmsModelFieldType::SELECT:
            case CmsModelFieldType::CHECKBOX:
            case CmsModelFieldType::IMAGE:
            case CmsModelFieldType::FILE:
                return "VARCHAR($field[maxLength])";
            case CmsModelFieldType::DATE:
                return "DATE";
            case CmsModelFieldType::DATETIME:
                return "DATETIME";
            case CmsModelFieldType::RICH_TEXT:
                return "TEXT";
        }
        return null;
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
}
