<?php


namespace ModStart\Field;

use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Util\ConvertUtil;

class ManyRelation extends AbstractField
{
    const MODE_GROUP_TAGS = 'groupTags';

    protected $isCustomField = true;
    protected $defaultValue = [];

    protected function setup()
    {
        $this->addVariables([
            'relationTable' => '',
            'relationModelIdKey' => '',
            'relationIdKey' => '',
            'mode' => self::MODE_GROUP_TAGS,
            'groupTags' => [],
            'groupTagsTitleKey' => 'title',
            'groupTagsChildKey' => '_child',
        ]);
        $this->hookValueSaved(function ($itemId, AbstractField $field) {
            $input = InputPackage::buildFromInput();
            $values = $input->getJson($field->column, []);
            ModelUtil::relationAssign(
                $this->getVariable('relationTable'),
                $this->getVariable('relationModelIdKey'),
                $itemId,
                $this->getVariable('relationIdKey'),
                $values
            );
        });
    }

    public function mode($mode)
    {
        $this->addVariables(['mode' => $mode]);
        return $this;
    }

    public function relationTable($table, $modelIdKey, $relationIdKey)
    {
        $this->addVariables(['relationTable' => $table]);
        $this->addVariables(['relationModelIdKey' => $modelIdKey]);
        $this->addVariables(['relationIdKey' => $relationIdKey]);
        return $this;
    }

    public function groupTags($groupTags, $groupTagsTitleKey = 'title', $groupTagsChildKey = '_child')
    {
        $this->mode(self::MODE_GROUP_TAGS);
        $this->addVariables([
            'groupTags' => $groupTags,
            'groupTagsTitleKey' => $groupTagsTitleKey,
            'groupTagsChildKey' => $groupTagsChildKey,
        ]);
        return $this;
    }


    public function renderView(AbstractField $field, $item, $index = 0)
    {
        $values = [];
        if ($item) {
            $relationTable = $field->getVariable('relationTable');
            $relationModelIdKey = $field->getVariable('relationModelIdKey');
            $relationIdKey = $field->getVariable('relationIdKey');
            $values = ModelUtil::values($relationTable, $relationIdKey, [$relationModelIdKey => $item->id]);
        }
        $this->value($values);
        return parent::renderView($field, $item, $index);
    }

    public function unserializeValue($value, AbstractField $field)
    {
        return ConvertUtil::toArray($value);
    }

    public function serializeValue($value, $model)
    {
        return json_encode($value);
    }

    public function prepareInput($value, $model)
    {
        return ConvertUtil::toArray($value);
    }

}
