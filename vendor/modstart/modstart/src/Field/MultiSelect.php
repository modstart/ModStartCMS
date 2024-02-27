<?php


namespace ModStart\Field;


use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Util\ConvertUtil;
use ModStart\Core\Util\SerializeUtil;
use ModStart\Core\Util\TagUtil;
use ModStart\Field\Concern\CanCascadeFields;

class MultiSelect extends AbstractField
{
    use CanCascadeFields;

    /**
     * 使用JSON
     */
    const SERIALIZE_TYPE_DEFAULT = null;
    /**
     * 使用冒号分割
     */
    const SERIALIZE_TYPE_COLON_SEPARATED = 1;

    protected $value = [];

    protected function setup()
    {
        $this->addVariables([
            'options' => [],
            'serializeType' => null,
        ]);
    }

    public function options($options)
    {
        $this->addVariables(['options' => $options]);
        return $this;
    }

    public function serializeType($value)
    {
        $this->addVariables(['serializeType' => $value]);
        return $this;
    }

    public function optionModel($table, $keyName = 'id', $labelName = 'name')
    {
        return $this->options(ModelUtil::valueMap($table, $keyName, $labelName));
    }

    public function optionType($typeCls)
    {
        return $this->options($typeCls::getList());
    }

    public function unserializeValue($value, AbstractField $field)
    {
        if (null === $value) {
            return $value;
        }
        switch ($this->getVariable('serializeType')) {
            case self::SERIALIZE_TYPE_COLON_SEPARATED:
                return TagUtil::string2Array($value);
            default:
                return ConvertUtil::toArray($value);
        }
    }

    public function serializeValue($value, $model)
    {
        switch ($this->getVariable('serializeType')) {
            case self::SERIALIZE_TYPE_COLON_SEPARATED:
                return TagUtil::array2String($value);
            default:
                if (is_array($value)) {
                    foreach ($value as $k => $v) {
                        if (is_numeric($v) && preg_match('/^\d+$/', $v)) {
                            $value[$k] = intval($v);
                        }
                    }
                }
                return SerializeUtil::jsonEncode($value);
        }
    }

    public function prepareInput($value, $model)
    {
        switch ($this->getVariable('serializeType')) {
            case self::SERIALIZE_TYPE_COLON_SEPARATED:
                return TagUtil::string2Array($value);
            default:
                return ConvertUtil::toArray($value, false);
        }
    }

    public function render()
    {
        $this->addCascadeScript();
        return parent::render();
    }

}
