<?php


namespace Module\Vendor\Provider\RichContent;


use ModStart\Core\Type\BaseType;


class RichContentProviderType implements BaseType
{
    public static function getList()
    {
        return array_build(RichContentProvider::all(), function ($k, $v) {
            
            return [$v->name(), $v->title()];
        });
    }

}
