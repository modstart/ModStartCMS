<?php


namespace ModStart\Field;


use ModStart\Core\Util\ConvertUtil;
use ModStart\Field\Concern\CanCascadeFields;

class SwitchField extends AbstractField
{
    use CanCascadeFields;

    protected $width = 80;

    protected function setup()
    {
        $this->addVariables([
            'options' => [
                1 => L('On'),
                0 => L('Off'),
            ],
        ]);
    }

    public function unserializeValue($value, AbstractField $field)
    {
        if (null === $value) {
            return $value;
        }
        return $value ? true : false;
    }

    public function serializeValue($value, $model)
    {
        return $value ? true : false;
    }

    public function prepareInput($value, $model)
    {
        return $value ? true : false;
    }

    public function optionsOnOff()
    {
        $this->addVariables(['options' => [
            1 => L('On'),
            0 => L('Off'),
        ]]);
        return $this;
    }

    public function optionsYesNo()
    {
        $this->addVariables(['options' => [
            1 => L('Yes'),
            0 => L('No'),
        ]]);
        return $this;
    }

    public function render()
    {
        $this->addCascadeScript();
        return parent::render();
    }
}
