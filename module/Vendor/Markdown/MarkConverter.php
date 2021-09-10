<?php


namespace Module\Vendor\Markdown;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use Webuni\CommonMark\TableExtension\TableExtension;

class MarkConverter extends CommonMarkConverter
{
    public function __construct(array $config = [], Environment $environment = null)
    {
        $environment = Environment::createCommonMarkEnvironment();
        $environment->addExtension(new TableExtension());
        parent::__construct($config, $environment);
    }
}

