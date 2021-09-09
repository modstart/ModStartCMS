<?php

namespace Module\Vendor\Markdown;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use Webuni\CommonMark\TableExtension\TableExtension;

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

class MarkConverter extends CommonMarkConverter
{
    public function __construct(array $config = [], Environment $environment = null)
    {
        $environment = Environment::createCommonMarkEnvironment();
        $environment->addExtension(new TableExtension());
        parent::__construct($config, $environment);
    }
}
