<?php


namespace Module\ContentBlock\Type;


use ModStart\Core\Type\BaseType;

class ContentBlockType implements BaseType
{
    const IMAGE = 'image';
    const HTML = 'html';

    public static function getList()
    {
        return [
            self::IMAGE => '图片',
            self::HTML => '富文本',
        ];
    }


}
