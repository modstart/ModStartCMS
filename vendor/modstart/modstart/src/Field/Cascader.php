<?php


namespace ModStart\Field;

use ModStart\Field\Concern\CanCascadeFields;

class Cascader extends AbstractField
{
    use CanCascadeFields;

    protected function setup()
    {
        $this->addVariables([
            'nodes' => [],
        ]);
    }

    public function nodes($value)
    {
        $this->addVariables(['nodes' => $value]);
        return $this;
    }

    public function render()
    {
        $this->addCascadeScript();
        return parent::render();
    }

}
