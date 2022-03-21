<?php


namespace ModStart\Field;


use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Util\ConvertUtil;
use ModStart\Core\Util\TagUtil;

class Tags extends AbstractField
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
            'tags' => [],
            'serializeType' => null,
        ]);
    }

    public function tags($value)
    {
        $this->addVariables(['tags' => $value]);
        return $this;
    }

    public function serializeType($value)
    {
        $this->addVariables(['serializeType' => $value]);
        return $this;
    }

    public function serializeAsColonSeparated()
    {
        $this->serializeType(self::SERIALIZE_TYPE_COLON_SEPARATED);
        return $this;
    }

    public function tagType($type)
    {
        return self::tags($type::getList());
    }

    public function tagModel($table, $keyName = 'id', $labelName = 'title')
    {
        return $this->tags(ModelUtil::valueMap($table, $keyName, $labelName));
    }

    public function tagModelField($table, $tagNameField = 'tag', $serializeType = null)
    {
        $values = ModelUtil::values($table, $tagNameField);
        $tags = [];
        foreach ($values as $value) {
            $tagList = null;
            switch ($serializeType) {
                case self::SERIALIZE_TYPE_COLON_SEPARATED:
                    $tagList = TagUtil::string2Array($value);
                    break;
                default:
                    $tagList = ConvertUtil::toArray($value);
                    break;
            }
            if (empty($tagList)) {
                continue;
            }
            foreach ($tagList as $v) {
                $tags[$v] = $v;
            }
        }
        return $this->tags($tags);
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
                return json_encode($value);
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
