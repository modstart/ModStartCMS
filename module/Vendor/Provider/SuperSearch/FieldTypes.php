<?php


namespace Module\Vendor\Provider\SuperSearch;


use ModStart\Core\Type\BaseType;

class FieldTypes implements BaseType
{
    const F_TEXT = 'text';
    const F_KEYWORD = 'keyword';
    const F_LONG = 'long';
    const F_LONG_ARRAY = 'longArray';
    const F_TEXT_ARRAY = 'textArray';

    public static function getList()
    {
        return [
            self::F_TEXT => '全文搜索字段',
            self::F_KEYWORD => '字符串',
            self::F_LONG => '整型',
            self::F_LONG_ARRAY => '整形数组',
            self::F_TEXT_ARRAY => '字符数组',
        ];
    }

}
