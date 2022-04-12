<?php


namespace ModStart\Field;

use ModStart\Core\Exception\BizException;

/**
 * Json字段
 * {} 或 []
 *
 * Class Json
 * @package ModStart\Field
 */
class Json extends AbstractField
{
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
