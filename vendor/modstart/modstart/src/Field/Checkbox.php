<?php


namespace ModStart\Field;


use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Util\ConvertUtil;
use ModStart\Field\Type\FieldRenderMode;

class Checkbox extends AbstractField
{
    protected $value = [];

    protected function setup()
    {
        $this->addVariables([
            'options' => [],
        ]);
    }

    public function options($options)
    {
        $this->addVariables(['options' => $options]);
        return $this;
    }

    public function optionModel($table, $keyName = 'id', $labelName = 'name')
    {
        return $this->options(ModelUtil::valueMap($table, $keyName, $labelName));
    }

    public function optionType($typeCls)
    {
        return $this->options($typeCls::getList());
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
