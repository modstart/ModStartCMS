<?php


namespace ModStart\Field;


class Datetime extends AbstractField
{
    protected $width = 160;

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
