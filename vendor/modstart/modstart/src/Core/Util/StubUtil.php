<?php


namespace ModStart\Core\Util;


class StubUtil
{
    public static function render($file, $variables = [], $base = null)
    {
        if (null === $base) {
            $base = base_path('vendor/modstart/modstart/resources/stub');
        }
        $content = file_get_contents("$base/$file.stub");
        $variables = array_build($variables, function ($k, $v) {
            return ['${' . $k . '}', $v];
        });
        return str_replace(array_keys($variables), array_values($variables), $content);
    }
}