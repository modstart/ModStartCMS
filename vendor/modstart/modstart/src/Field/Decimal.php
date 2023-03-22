<?php


namespace ModStart\Field;


class Decimal extends AbstractField
{
    protected $view = 'modstart::core.field.number';
    protected $rules = ['regex:/^-?\\d+(\\.\\d+)?$/i'];

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
