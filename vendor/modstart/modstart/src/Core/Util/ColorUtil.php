<?php


namespace ModStart\Core\Util;


class ColorUtil
{
    private static $colors = [
        '#FF7F50',
        '#DC143C',
        '#9932CC',
        '#B22222',
        '#DAA520',
        '#FF69B4',
        '#20B2AA',
        '#9370DB',
        '#3CB371',
        '#7B68EE',
        '#C71585',
        '#191970',
        '#FF8C00',
        '#DB7093',
        '#CD853F',
        '#BC8F8F',
        '#EE82EE',
        '#8B4513',
        '#FA8072',
        '#2E8B57',
        '#6B8E23',
        '#FF6347',
        '#ED3F14',
        '#4F7FF3',
        '#6A46BD',
        '#E9BD6C',
    ];

    public static function randomColors()
    {
        return self::$colors;
    }

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

    public static function adjust($hexColor, $steps)
    {
        $hexColor = strtoupper($hexColor);
        if (!preg_match('/^#[A-F0-9]{6}$/', $hexColor)) {
            return $hexColor;
        }

        $hexColor = str_replace('#', '', $hexColor);
        $rHex = substr($hexColor, 0, 2);
        $gHex = substr($hexColor, 2, 2);
        $bHex = substr($hexColor, 4, 2);

        $r = hexdec($rHex);
        $g = hexdec($gHex);
        $b = hexdec($bHex);

        $r = max(0, min(255, $r + $steps));
        $g = max(0, min(255, $g + $steps));
        $b = max(0, min(255, $b + $steps));

        return '#' . strtoupper(join('', [
                str_pad(dechex($r), 2, '0'),
                str_pad(dechex($g), 2, '0'),
                str_pad(dechex($b), 2, '0'),
            ]));

    }
}
