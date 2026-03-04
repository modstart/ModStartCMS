<?php


namespace ModStart\Core\Util;


/**
 * @Util 模板文件渲染工具
 */
class StubUtil
{
    /**
     * @Util 读取 .stub 模板文件并替换变量占位符
     * @param $file string 模板文件路径（相对于 base 目录，无需 .stub 后缀）
     * @param $variables array 变量占位符映射，如 ['Name' => 'User']将会替换 ${Name}
     * @param $base string|null 模板根目录，默认为 vendor stub 目录
     * @return string
     */
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