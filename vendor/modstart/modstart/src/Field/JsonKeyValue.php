<?php


namespace ModStart\Field;

use ModStart\Core\Exception\BizException;
use ModStart\Core\Util\SerializeUtil;

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
        return SerializeUtil::jsonEncode($value);
    }

    public function prepareInput($value, $model)
    {
        $json = @json_decode($value, true);
        BizException::throwsIf($this->label . ' ' . L('Json Format Error'), $value && null === $json);
        return $json;
    }
}
