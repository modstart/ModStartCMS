<?php


namespace ModStart\Core\Util;


class ColorUtil
{
    private static $colors = [
        '#4F7FF3',
        '#5BC692',
        '#F0A453',
        '#6A46BD',
        '#e9bd6c',
        'rgb(237, 63, 20)',
    ];

    public static function randomColor()
    {
        static $index = 0;
        $color = self::$colors[($index++) % (count(self::$colors))];
        return $color;
    }

    public static function pick($hashString)
    {
        $index = abs(crc32($hashString)) % count(self::$colors);
        return self::$colors[$index];
    }
}
