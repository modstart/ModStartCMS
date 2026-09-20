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

    public static function smart($string)
    {
        $perpers = [
            '/success/i' => '#5cb85c',
            '/info/i' => '#5bc0de',
            '/warning/i' => '#f0ad4e',
            '/danger/i' => '#d9534f',
            '/error/i' => '#d9534f',
        ];
        foreach ($perpers as $perper => $color) {
            if (preg_match($perper, $string)) {
                return $color;
            }
        }
        return self::pick($string);
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
                str_pad(dechex($r), 2, '0', STR_PAD_LEFT),
                str_pad(dechex($g), 2, '0', STR_PAD_LEFT),
                str_pad(dechex($b), 2, '0', STR_PAD_LEFT),
            ]));

    }

    /**
     * 将 #FFFFFFFF/#FFFFFF 转换为 rgba(255,255,255,1)
     * @param $hexColor string 颜色值，RGBA
     * @return string rgba(255,255,255,1)
     */
    public static function hexToRgba($hexColor)
    {
        $hexColor = strtoupper($hexColor);
        if (preg_match('/^#([A-F0-9]{2})([A-F0-9]{2})([A-F0-9]{2})$/', $hexColor, $mat)) {
            $r = hexdec($mat[1]);
            $g = hexdec($mat[2]);
            $b = hexdec($mat[3]);
            return "rgba({$r},{$g},{$b},1)";
        } else if (preg_match('/^#([A-F0-9]{2})([A-F0-9]{2})([A-F0-9]{2})([A-F0-9]{2})$/', $hexColor, $mat)) {
            $r = hexdec($mat[1]);
            $g = hexdec($mat[2]);
            $b = hexdec($mat[3]);
            $a = round(hexdec($mat[4]) / 255, 2);
            return "rgba({$r},{$g},{$b},{$a})";
        }
        return "rgba(0,0,0,1)";
    }

    /**
     * 将 #FFFFFFFF/#FFFFFF 转换为 [ 'r' => 255, 'g' => 255, 'b' => 255, 'a' => 1 ]
     * @param $hexColor string 颜色值，RGBA
     * @return array
     */
    public static function hexToRgbaArray($hexColor)
    {
        $hexColor = strtoupper($hexColor);
        $result = [
            'r' => 0,
            'g' => 0,
            'b' => 0,
            'a' => 1,
        ];
        if (preg_match('/^#([A-F0-9]{2})([A-F0-9]{2})([A-F0-9]{2})$/', $hexColor, $mat)) {
            $result['r'] = hexdec($mat[1]);
            $result['g'] = hexdec($mat[2]);
            $result['b'] = hexdec($mat[3]);
        } else if (preg_match('/^#([A-F0-9]{2})([A-F0-9]{2})([A-F0-9]{2})([A-F0-9]{2})$/', $hexColor, $mat)) {
            $result['r'] = hexdec($mat[1]);
            $result['g'] = hexdec($mat[2]);
            $result['b'] = hexdec($mat[3]);
            $result['a'] = round(hexdec($mat[4]) / 255, 2);
        }
        return $result;
    }

    /**
     * 由主色生成 CSS 变量块（可直接注入 <style>，主题自定义主色时使用）
     *
     * 输出旧变量 --theme-color-*（老主题 CSS 直接使用，新设计系统 base/theme.less 也会
     * 读取它们做桥接），并补齐新设计系统遗漏的派生值：
     * 浅色底 --theme-color-primary-light-bg、半透明色 --theme-color-primary-soft/-glow、
     * 焦点环 --theme-color-focus-ring，以及供 CSS 计算透明度变体的 --theme-color-primary-rgb。
     *
     * @param $hexColor string 主色，如 #2F6BFF
     * @return string ':root { ... }'，颜色非法时仅返回基础三个变量（保持与旧行为一致）
     */
    public static function primaryColorCss($hexColor)
    {
        $hexColor = strtoupper($hexColor);
        $variables = [
            '--theme-color-primary: ' . $hexColor . ';',
            '--theme-color-primary-light: ' . self::adjust($hexColor, 20) . ';',
            '--theme-color-primary-dark: ' . self::adjust($hexColor, -20) . ';',
        ];
        if (preg_match('/^#[A-F0-9]{6}$/', $hexColor)) {
            $rgb = self::hexToRgbaArray($hexColor);
            $rgbText = $rgb['r'] . ',' . $rgb['g'] . ',' . $rgb['b'];
            $variables[] = '--theme-color-primary-rgb: ' . $rgbText . ';';
            // 浅色底：默认主色 #2F6BFF 对应的 #EDF3FF 约等于 8.5% 主色 + 白色
            $variables[] = '--theme-color-primary-light-bg: ' . self::mixWithWhite($hexColor, 0.085) . ';';
            // 透明度与 base/theme.less 里的默认值保持一致
            $alphaVariables = [
                '--theme-color-primary-soft' => 0.1,
                '--theme-color-primary-glow' => 0.24,
                '--theme-color-focus-ring' => 0.16,
            ];
            foreach ($alphaVariables as $name => $alpha) {
                $variables[] = $name . ': rgba(' . $rgbText . ',' . $alpha . ');';
            }
        }
        return ":root {\n    " . join("\n    ", $variables) . "\n}";
    }

    /**
     * 将颜色按比例与白色混合（得到更浅的底色）
     * @param $hexColor string 颜色值，如 #2F6BFF
     * @param $ratio float 颜色占比 0~1，越小白越接近纯白
     * @return string 混合后的颜色，如 #EDF2FF
     */
    private static function mixWithWhite($hexColor, $ratio)
    {
        $rgb = self::hexToRgbaArray($hexColor);
        $mixed = [];
        foreach (['r', 'g', 'b'] as $key) {
            $mixed[$key] = (int)round($rgb[$key] * $ratio + 255 * (1 - $ratio));
        }
        return sprintf('#%02X%02X%02X', $mixed['r'], $mixed['g'], $mixed['b']);
    }

    /**
     * @param $hexColor
     * @return void true=深色,false=浅色
     */
    public static function isDark($hexColor)
    {
        $rgba = self::hexToRgbaArray($hexColor);
        $brightness = (($rgba['r'] * 299) + ($rgba['g'] * 587) + ($rgba['b'] * 114)) / 1000;
        return $brightness < 128;
    }
}
