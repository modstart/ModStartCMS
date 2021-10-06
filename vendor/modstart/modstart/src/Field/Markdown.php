<?php


namespace ModStart\Field;


class Markdown extends AbstractField
{
    protected $listable = false;
    protected static $js = [
        'asset/common/editorMarkdown.js',
    ];
    protected static $css = [
    ];
}
