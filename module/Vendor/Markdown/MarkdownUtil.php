<?php

namespace Module\Vendor\Markdown;

use Illuminate\Support\Str;

class MarkdownUtil
{
    public static function convertToHtml($markdown)
    {
        if (PHP_VERSION_ID >= 80000) {
            return Str::of($markdown)->markdown();
        }
        $converter = new MarkConverter([
            'renderer' => [
                'soft_break' => "<br />",
            ],
        ]);
        return $converter->convertToHtml($markdown);
    }
}

