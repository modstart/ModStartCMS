<?php


namespace ModStart\Field\Type;


use ModStart\Core\Type\BaseType;

class CustomFieldType implements BaseType
{
    const TYPE_TEXT = 'Text';
    const TYPE_RADIO = 'Radio';
    const TYPE_FILE = 'File';
    const TYPE_FILES = 'Files';

    public static function getList()
    {
        return [
            self::TYPE_TEXT => '文本',
            self::TYPE_RADIO => '单选',
            self::TYPE_FILE => '单文件',
            self::TYPE_FILES => '多文件',
        ];
    }

    public static function isValid($type)
    {
        $map = static::getList();
        return isset($map[$type]);
    }

}
