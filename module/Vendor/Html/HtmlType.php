<?php

namespace Module\Vendor\Html;

use ModStart\Core\Type\BaseType;

/**
 * Class HtmlType
 * @package Module\Vendor\Html
 * @deprecated
 * @see \Module\Vendor\Provider\RichContent\RichContentProvider
 */
class HtmlType implements BaseType
{
    const RICH_TEXT = 1;
    const MARKDOWN = 2;
    const SIMPLE_TEXT = 3;

    public static function getList()
    {
        return [
            self::RICH_TEXT => '富文本',
            self::MARKDOWN => 'Markdown',
            self::SIMPLE_TEXT => '简单文本',
        ];
    }

}
