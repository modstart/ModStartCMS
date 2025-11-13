<?php


namespace ModStart\Core\Util;

class XKeywordsUtil
{
    /**
     * 规则匹配
     * @param $keywords string 关键词，使用逗号或换行符分割
     * @param $content string 需要匹配的内容
     * @param $matKeyword string 匹配到的关键词
     */
    public static function match($keywords, $content, &$matKeyword = null)
    {
        if (empty($keywords) || empty($content)) {
            return false;
        }
        $list = explode("\n", $keywords);
        foreach ($list as $line) {
            $line = trim($line);
            if ($line == '') {
                continue;
            }
            $items = explode(',', $line);
            foreach ($items as $item) {
                $item = trim($item);
                if ($item == '') {
                    continue;
                }
                if (false !== strpos($content, $item)) {
                    $matKeyword = $item;
                    return true;
                } else if (substr($item, 0, 1) === '@') {
                    $regex = substr($item, 1);
                    if (@preg_match('/' . $regex . '/', $content)) {
                        $matKeyword = $item;
                        return true;
                    }
                } else if (false !== strpos($item, ' ')) {
                    $allMatch = true;
                    foreach (explode(' ', $item) as $k) {
                        if (false === strpos($content, $k)) {
                            $allMatch = false;
                            break;
                        }
                    }
                    if ($allMatch) {
                        $matKeyword = $item;
                        return true;
                    }
                } else if (preg_match('/((\\*)(\\d+))[^\\d]/', $item, $mat)) {
                    $ks = explode($mat[1], $item);
                    $regx = '/' . preg_quote($ks[0]) . '.{3,' . $mat[3] * 3 . '}' . preg_quote($ks[1]) . '/';
                    if (@preg_match($regx, $content)) {
                        $matKeyword = $item;
                        return true;
                    }
                } else if (false !== strpos($item, '*')) {
                    $ks = explode('*', $item);
                    $regx = '/' . preg_quote($ks[0]) . '.+' . preg_quote($ks[1]) . '/';
                    if (@preg_match($regx, $content)) {
                        $matKeyword = $item;
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public static function descriptionHtml()
    {
        return "<div><div><a href='javascript:;' onclick='$(this).parent().next().toggle();'><i class='iconfont icon-description'></i>规则使用说明</a></div><div class='tw-hidden'><pre class='ub-content-bg'>" . self::description() . "</pre></div></div>";
    }

    public static function description()
    {
        $text = <<< TEXT
匹配规则
· 简单包含: "你好" 匹配 "你好"
· 同时包含: "你 好" 匹配 "你们好"、"你们好"、"很好你们"
· 顺序同时包含: "你*好" 匹配 "你们好"、"你们很好"，*匹配一个或多个字
· 顺序限定同时包含: "你*1好" 匹配 "你们好"，不能匹配"你们很好"，数字表示最长多少个汉字字符，1个汉字=3英文字母
· 正则表达式："@正则表达式" 匹配 "正则表达式"，@开头表示正则表达式，如果正则中包含逗号，需要将正则写在独立一行
使用说明
· 多个正则使用半角逗号(,)或换行分割
TEXT;

        return $text;
    }


    public static function test()
    {
        $tests = [
            ['你好', '你好', true,],
            ['你好', '你们好', false,],
            ['你 好', '你们好', true,],
            ['你*好', '你们好', true,],
            ['你*1好', '你们好', true,],
            ['你*1好', '你们很好', false,],
            ['你*2好', '你们很好', true,],
            ['@你们', '你们很好', true,],
            ['@你好', '你们很好', false,],
        ];
        $results = [];
        foreach ($tests as $test) {
            $match = self::match($test[0], $test[1]);
            echo sprintf(
                    '%-10s%-30s%-10s%-10s',
                    $test[0],
                    $test[1],
                    $match ? 'true' : 'false',
                    $test[2] == $match ? 'PASS' : 'FAIL'
                ) . PHP_EOL;
        }
    }
}
