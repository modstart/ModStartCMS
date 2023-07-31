<?php


namespace ModStart\Core\Provider;


class DefaultFontProvider extends AbstractFontProvider
{
    public function name()
    {
        return 'default';
    }

    public function title()
    {
        return '默认字体';
    }

    public function path()
    {
        return base_path('vendor/modstart/modstart/resources/font/AlibabaPuHuiTi-2-55-Regular.simple.ttf');
    }
}
