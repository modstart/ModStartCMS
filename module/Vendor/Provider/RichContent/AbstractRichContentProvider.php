<?php


namespace Module\Vendor\Provider\RichContent;

use Module\Vendor\Html\HtmlConvertUtil;

abstract class AbstractRichContentProvider
{
    abstract public function name();

    abstract public function title();

    abstract public function render($name, $value, $param = []);

    public function toHtml($value, $htmlInterceptors = null)
    {
        return HtmlConvertUtil::callInterceptors($htmlInterceptors, $value);
    }

}
