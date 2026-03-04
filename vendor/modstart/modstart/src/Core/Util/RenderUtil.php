<?php


namespace ModStart\Core\Util;


use Illuminate\Support\Facades\View;

/**
 * @Util 视图渲染工具
 */
class RenderUtil
{
    /**
     * @Util 渲染指定视图为字符串
     * @param $view string 视图名称
     * @param $data array 传入视图的数据
     * @return string
     */
    public static function view($view, $data = [])
    {
        return View::make($view, $data)->render();
    }

    /**
     * @Util 判断视图是否存在
     * @param $view string 视图名称
     * @return bool
     */
    public static function viewExists($view)
    {
        return View::exists($view);
    }

    /**
     * @Util 渲染视图并提取内嵌 <script> 标签内的 JS 代码
     * @param $view string 视图名称
     * @param $data array 传入视图的数据
     * @return string
     */
    public static function viewScript($view, $data = [])
    {
        $content = trim(self::view($view, $data));
        $content = preg_replace('/^<script>/', '', $content);
        $content = preg_replace('/<\/script>$/', '', $content);
        return trim($content);
    }

    /**
     * @Util 对内容进行安全输出处理（可选 HTML 转义，并修复 @parent 问题）
     * @param $content string 内容
     * @param $htmlSpecialChars bool 是否对内容进行 HTML 转义
     * @return string
     */
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
