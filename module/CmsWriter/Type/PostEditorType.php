<?php


namespace Module\CmsWriter\Type;


use ModStart\Core\Type\BaseType;
use Module\Vendor\Html\HtmlType;

class PostEditorType implements BaseType
{
    const RICH_TEXT = HtmlType::RICH_TEXT;
    const MARKDOWN = HtmlType::MARKDOWN;

    public static function getList()
    {
        return [
            self::RICH_TEXT => '富文本',
            self::MARKDOWN => 'Markdown',
        ];
    }

}
