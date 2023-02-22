<?php


namespace ModStart\Field;


class Rate extends AbstractField
{
    protected function setup()
    {
        $this->addVariables([
            'maxValue' => 10,
        ]);
    }

    public function maxValue($maxValue)
    {
        $this->addVariables(['maxValue' => $maxValue]);
        return $this;
    }

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
