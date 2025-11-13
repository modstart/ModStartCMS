<?php


namespace ModStart\Field\Type;


use ModStart\Core\Type\BaseType;

class ComplexFieldsType implements BaseType
{
    const TYPE_DISPLAY = 'display';
    const TYPE_SWITCH = 'switch';
    const TYPE_TEXT = 'text';
    const TYPE_TEXTAREA = 'textarea';
    const TYPE_ICON = 'icon';
    const TYPE_IMAGE = 'image';
    const TYPE_VALUES = 'values';
    const TYPE_NUMBER = 'number';
    const TYPE_NUMBER_TEXT = 'numberText';
    const TYPE_SLIDER = 'slider';
    const TYPE_LINK = 'link';
    const TYPE_SELECT = 'select';
    const TYPE_RADIO = 'radio';
    const TYPE_COLOR = 'color';
    const TYPE_RICH_HTML = 'richHtml';

    public static function getList()
    {
        return [
            self::TYPE_DISPLAY => '只读文本',
            self::TYPE_SWITCH => '开关',
            self::TYPE_TEXT => '单行文本',
            self::TYPE_TEXTAREA => '多行文本',
            self::TYPE_ICON => '图标',
            self::TYPE_IMAGE => '图片',
            self::TYPE_VALUES => '多字符串值',
            self::TYPE_NUMBER => '数字',
            self::TYPE_NUMBER_TEXT => '数字文本',
            self::TYPE_SLIDER => '滑动数字',
            self::TYPE_LINK => '链接',
            self::TYPE_SELECT => '下拉选择',
            self::TYPE_RADIO => '单选按钮',
            self::TYPE_COLOR => '颜色选择',
            self::TYPE_RICH_HTML => '富文本',
        ];
    }
}
