<?php


namespace Module\ContentBlock\Type;


use ModStart\Core\Type\BaseType;

class ContentBlockType implements BaseType
{
    const BASIC = 'basic';
    const IMAGE = 'image';
    const HTML = 'html';

    public static function getList()
    {
        return [
            self::BASIC => '通用',
            self::IMAGE => '单图',
            self::HTML => '富文本',
        ];
    }


}
