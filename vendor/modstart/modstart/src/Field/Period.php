<?php


namespace ModStart\Field;


class Period extends AbstractField
{
    public function unserializeValue($value, AbstractField $field)
    {
        return $value;
    }

    public function serializeValue($value, $model)
    {
        return $value;
    }

    public function prepareInput($value, $model)
    {
        $pcs = explode(':', $value);
        $list = [
            isset($pcs[0]) ? $pcs[0] : 0,
            isset($pcs[1]) ? $pcs[1] : 0,
            isset($pcs[2]) ? $pcs[2] : 0,
        ];
        foreach ($list as $k => $v) {
            $list[$k] = max(intval($v), 0);
        }
        return sprintf('%02d:%02d:%02d', $list[0], $list[1], $list[2]);
    }
}
