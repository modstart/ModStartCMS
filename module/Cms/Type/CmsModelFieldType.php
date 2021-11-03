<?php


namespace Module\Cms\Type;


use ModStart\Core\Type\BaseType;

class CmsModelFieldType implements BaseType
{
    const TEXT = 'text';
    const TEXTAREA = 'textarea';
    const RADIO = 'radio';
    const SELECT = 'select';
    const CHECKBOX = 'checkbox';
    const IMAGE = 'image';
    const FILE = 'file';
    const DATE = 'date';
    const DATETIME = 'datetime';
    const RICH_TEXT = 'richText';

    public static function getList()
    {
        return [
            self::TEXT => '单行文本',
            self::TEXTAREA => '多行文本',
            self::RADIO => '单选按钮',
            self::SELECT => '下拉选择',
            self::CHECKBOX => '多选按钮',
            self::IMAGE => '图片',
            self::FILE => '文件',
            self::DATE => '日期',
            self::DATETIME => '日期时间',
            self::RICH_TEXT => '富文本',
        ];
    }
}
