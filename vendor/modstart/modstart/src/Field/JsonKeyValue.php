<?php


namespace ModStart\Field;

use ModStart\Core\Exception\BizException;

/**
 * Json键值字段
 *
 * {"键":"值",...}
 *
 * Class JsonKeyValue
 * @package ModStart\Field
 */
class JsonKeyValue extends AbstractField
{
    protected $width = 200;

    public function unserializeValue($value, AbstractField $field)
    {
        return @json_decode($value, true);
    }

    public function serializeValue($value, $model)
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }

    public function prepareInput($value, $model)
    {
        $json = @json_decode($value, true);
        BizException::throwsIf($this->label . ' ' . L('Json Format Error'), $value && null === $json);
        return $json;
    }
}
