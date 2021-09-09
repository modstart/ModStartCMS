<?php


namespace ModStart\Field;

use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Util\ConvertUtil;
use ModStart\Field\Concern\CanCascadeFields;
use ModStart\Field\Type\FieldRenderMode;

class Radio extends AbstractField
{
    use CanCascadeFields;

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

    public function optionType($cls)
    {
        return $this->options($cls::getList());
    }

    public function optionModel($table, $keyName = 'id', $labelName = 'name')
    {
        return $this->options(ModelUtil::valueMap($table, $keyName, $labelName));
    }

    public function render()
    {
        $this->addCascadeScript();
        return parent::render();
    }

}
