<?php


namespace Module\Cms\Type;


use ModStart\Core\Type\BaseType;

class CmsMode implements BaseType
{
    const LIST_DETAIL = 1;
    const PAGE = 2;
    const FORM = 3;

    public static function getList()
    {
        return [
            self::LIST_DETAIL => '列表+详情',
            self::PAGE => '单页',
            self::FORM => '表单',
        ];
    }
}