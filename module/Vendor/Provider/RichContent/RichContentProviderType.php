<?php


namespace Module\Vendor\Provider\RichContent;


use ModStart\Core\Type\BaseType;

/**
 * Class RichContentProviderType
 * @package Module\Vendor\Provider\RichContent
 * @since 1.8.0
 */
class RichContentProviderType implements BaseType
{
    public static function getList()
    {
        return array_build(RichContentProvider::all(), function ($k, $v) {
            /** @var $v AbstractRichContentProvider */
            return [$v->name(), $v->title()];
        });
    }

}
