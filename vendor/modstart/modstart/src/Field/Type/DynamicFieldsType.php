<?php


namespace ModStart\Field\Type;


use ModStart\Core\Type\BaseType;

class DynamicFieldsType implements BaseType
{
    const TYPE_TEXT = 'text';
    const TYPE_TEXTAREA = 'textarea';
    const TYPE_NUMBER = 'number';
    const TYPE_SWITCH = 'switch';
    const TYPE_RADIO = 'radio';
    const TYPE_SELECT = 'select';
    const TYPE_CHECKBOX = 'checkbox';
    const TYPE_FILE = 'file';
    const TYPE_FILES = 'files';

    public static function getList()
    {
        return [
            self::TYPE_TEXT => '单行文本',
            self::TYPE_TEXTAREA => '多行文本',
            self::TYPE_NUMBER => '数字',
            self::TYPE_SWITCH => '开关',
            self::TYPE_RADIO => '单选',
            self::TYPE_SELECT => '下拉选项',
            self::TYPE_CHECKBOX => '多选',
            self::TYPE_FILE => '单个文件',
            self::TYPE_FILES => '多个文件',
        ];
    }


}
