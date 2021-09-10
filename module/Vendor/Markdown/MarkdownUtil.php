<?php

namespace Module\Vendor\Markdown;

class MarkdownUtil
{
    public static function convertToHtml($markdown)
    {
        $converter = new MarkConverter([
            'renderer' => [
                'soft_break' => "<br />",
            ],
        ]);
        return $converter->convertToHtml($markdown);
    }
}

