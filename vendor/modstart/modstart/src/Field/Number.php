<?php


namespace ModStart\Field;


class Number extends AbstractField
{
    public function unserializeValue($value, AbstractField $field)
    {
        if ('' === $value || null === $value) {
            return null;
        }
        return intval($value);
    }

    public function serializeValue($value, $model)
    {
        if ('' === $value || null === $value) {
            return null;
        }
        return intval($value);
    }

    public function prepareInput($value, $model)
    {
        if ('' === $value || null === $value) {
            return null;
        }
        return intval($value);
    }
}
