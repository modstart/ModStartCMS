<?php


namespace ModStart\Field;


/**
 * Json多组键值对字段
 * [
 *   {"k":"键","v":"值"},
 *   ...
 * ]
 *
 * Class KeyValueList
 * @package ModStart\Field
 */
class KeyValueList extends AbstractField
{
    protected $width = 300;
    protected $listable = false;

    protected function setup()
    {
        $this->addVariables([
            'keyTitle' => L('Key'),
            'valueTitle' => L('Value'),
            'keyPlaceholder' => L('Please Input'),
            'valuePlaceholder' => L('Please Input'),
            'keyLabel' => 'k',
            'valueLabel' => 'v',
        ]);
    }

    public function keyPlaceholder($value)
    {
        $this->addVariables(['keyPlaceholder' => $value]);
        return $this;
    }

    public function valuePlaceholder($value)
    {
        $this->addVariables(['valuePlaceholder' => $value]);
        return $this;
    }

    public function keyTitle($value)
    {
        $this->addVariables(['keyTitle' => $value]);
        return $this;
    }

    public function valueTitle($value)
    {
        $this->addVariables(['valueTitle' => $value]);
        return $this;
    }

    public function keyLabel($keyLabel)
    {
        $this->addVariables(['keyLabel' => $keyLabel]);
        return $this;
    }

    public function valueLabel($valueLabel)
    {
        $this->addVariables(['valueLabel' => $valueLabel]);
        return $this;
    }

    public function unserializeValue($value, AbstractField $field)
    {
        if (null === $value) {
            return $value;
        }
        $value = @json_decode($value, true);
        if (empty($value)) {
            $value = [];
        }
        return $value;
    }

    public function serializeValue($value, $model)
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }

    public function prepareInput($value, $model)
    {
        $value = @json_decode($value, true);
        if (empty($value)) {
            $value = [];
        }
        return $value;
    }
}
