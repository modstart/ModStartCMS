<?php


namespace ModStart\Field;


class RichHtml extends AbstractField
{
    protected $listable = false;
    protected static $js = [
        'asset/common/editor.js',
    ];
}