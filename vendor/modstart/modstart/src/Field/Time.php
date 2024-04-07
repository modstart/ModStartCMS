<?php


namespace ModStart\Field;


class Time extends AbstractField
{
    /**
     * 使用JSON
     */
    const SERIALIZE_TYPE_DEFAULT = null;
    /**
     * 使用冒号分割
     */
    const SERIALIZE_TYPE_SECOND = 1;

    protected function setup()
    {
        $this->addVariables([
            'serializeType' => null,
        ]);
    }

    public function serializeType($value)
    {
        $this->addVariables(['serializeType' => $value]);
        return $this;
    }

    public function serializeAsSecond()
    {
        $this->serializeType(self::SERIALIZE_TYPE_SECOND);
        return $this;
    }

    public function unserializeValue($value, AbstractField $field)
    {
        if ('' === $value || null === $value) {
            return null;
        }
        switch ($this->getVariable('serializeType')) {
            case self::SERIALIZE_TYPE_SECOND:
                return sprintf('%02d:%02d:%02d', $value / 3600, $value / 60 % 60, $value % 60);
            default:
                return $value;
        }
    }

    public function serializeValue($value, $model)
    {
        if ('' === $value || null === $value) {
            return null;
        }
        switch ($this->getVariable('serializeType')) {
            case self::SERIALIZE_TYPE_SECOND:
                $time = explode(':', $value);
                if (count($time) !== 3) {
                    return 0;
                }
                return $time[0] * 3600 + $time[1] * 60 + $time[2];
            default:
                return $value;
        }
    }

    public function prepareInput($value, $model)
    {
        if ('' === $value || null === $value) {
            return null;
        }
        return $value;
    }
}
