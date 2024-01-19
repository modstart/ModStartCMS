<?php


namespace ModStart\Core\Util;


use Illuminate\Support\Facades\View;

class RenderUtil
{
    public static function view($view, $data = [])
    {
        return View::make($view, $data)->render();
    }

    public static function viewExists($view)
    {
        return View::exists($view);
    }

    public static function viewScript($view, $data = [])
    {
        $content = trim(self::view($view, $data));
        $content = preg_replace('/^<script>/', '', $content);
        $content = preg_replace('/<\/script>$/', '', $content);
        return trim($content);
    }

    public static function display($content, $htmlSpecialChars = false)
    {
        if ($htmlSpecialChars) {
            $content = htmlspecialchars($content);
        }
        $replaces = [
            // 这是Laravel一个长久Bug，暂时无法解决
            // https://github.com/laravel/framework/issues/7888
            // https://github.com/laravel/framework/issues/28693
            '@parent' => '&#64;parent',
        ];
        return str_replace(array_keys($replaces), array_values($replaces), $content);
    }
}
