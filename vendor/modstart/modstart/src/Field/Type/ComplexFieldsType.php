<?php


namespace ModStart\Field\Type;


use ModStart\Core\Type\BaseType;

class ComplexFieldsType implements BaseType
{
    const TYPE_TEXT = 'text';
    const TYPE_SWITCH = 'switch';
    const TYPE_ICON = 'icon';
    const TYPE_NUMBER = 'number';

    public static function getList()
    {
        return [
            self::TYPE_TEXT => '文本',
            self::TYPE_SWITCH => '开关',
            self::TYPE_ICON => '图标',
            self::TYPE_NUMBER => '数字',
        ];
    }
}
