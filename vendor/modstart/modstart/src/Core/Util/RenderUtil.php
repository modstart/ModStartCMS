<?php


namespace ModStart\Core\Util;


use Illuminate\Support\Facades\View;

class RenderUtil
{
    public static function view($view, $data = [])
    {
        return View::make($view, $data)->render();
    }

    public static function viewScript($view, $data = [])
    {
        $content = trim(self::view($view, $data));
        $content = preg_replace('/^<script>/', '', $content);
        $content = preg_replace('/<\/script>$/', '', $content);
        return trim($content);
    }
}
