<?php


namespace ModStart\Field;


class Hidden extends AbstractField
{
    protected function setup()
    {
        $this->addVariables([
            'type' => 'text',
        ]);
    }

    public function type($value)
    {
        $this->setVariable('type', $value);
        return $this;
    }

    public function typeNumber()
    {
        return $this->type('number');
    }

    public function typeFloat()
    {
        return $this->type('float');
    }

    public function typeBool()
    {
        return $this->type('bool');
    }

    public function typeText()
    {
        return $this->type('text');
    }

    public function unserializeValue($value, AbstractField $field)
    {
        if (null === $value) {
            return $value;
        }
        switch ($this->getVariable('type')) {
            case 'number':
                return intval($value);
            case 'float':
                return floatval($value);
            case 'bool':
                return boolval($value);
            default:
                return $value;
        }
    }

    public function serializeValue($value, $model)
    {
        if (null === $value) {
            return $value;
        }
        switch ($this->getVariable('type')) {
            case 'number':
                return intval($value);
            case 'float':
                return floatval($value);
            case 'bool':
                return boolval($value);
            default:
                return $value;
        }
    }

    public function prepareInput($value, $model)
    {
        return $value;
    }
}
