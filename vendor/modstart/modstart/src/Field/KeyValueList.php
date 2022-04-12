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
    protected $value = [];
    protected $width = 300;
    protected $listable = false;

    protected function setup()
    {
        $this->addVariables([
            'keyLabel' => 'k',
            'valueLabel' => 'v',
        ]);
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
        $value = @json_decode($value, true);
        if (empty($value)) {
            $value = [];
        }
        return $value;
    }

    public function serializeValue($value, $model)
    {
        return json_encode($value);
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
