<?php


namespace ModStart\Field;


class Decimal extends AbstractField
{
    protected $view = 'modstart::core.field.number';
    protected $rules = ['regex:/^-?\\d+(\\.\\d+)?$/i'];

    protected function setup()
    {
        $this->addVariables([
            // 自动着色，正数绿色，负数红色
            'autoColor' => false,
            // 是否显示符号
            'signShow'=>false,
        ]);
    }

    public function autoColor($value)
    {
        $this->setVariable('autoColor', $value);
        return $this;
    }

    public function signShow($value)
    {
        $this->setVariable('signShow', $value);
        return $this;
    }

    public function unserializeValue($value, AbstractField $field)
    {
        if ('' === $value || null === $value) {
            return null;
        }
        return $value;
    }

    public function serializeValue($value, $model)
    {
        if ('' === $value || null === $value) {
            return null;
        }
        return $value;
    }

    public function prepareInput($value, $model)
    {
        if ('' === $value || null === $value) {
            return null;
        }
        return $value;
    }
}
