<?php


namespace ModStart\Field;


use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Util\ConvertUtil;
use ModStart\Core\Util\TagUtil;

class Checkbox extends AbstractField
{
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
                return json_encode($value, JSON_UNESCAPED_UNICODE);
        }
    }

    public function prepareInput($value, $model)
    {
        switch ($this->getVariable('serializeType')) {
            case self::SERIALIZE_TYPE_COLON_SEPARATED:
                return TagUtil::string2Array($value);
            default:
                return ConvertUtil::toArray($value);
        }
    }

}
